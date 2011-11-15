<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Controller;

  use DeBaasMedia\Bundle\ContactBundle\Form\Type\ContactRequestType
    , DeBaasMedia\Bundle\ContactBundle\Model\ContactRequest
    , Symfony\Component\HttpKernel\Exception\HttpException
    , Symfony\Bundle\FrameworkBundle\Controller\Controller;

  /**
   * ContactController
   *
   * @author  Marijn Huizendveld <marijn.huizendveld@gmail.com>
   */
  class ContactController extends Controller
  {

    /**
     * Form controller
     *
     * @return  Response
     */
    public function formAction ()
    {
      $form = $this->createForm(new ContactRequestType(), new ContactRequest());

      if ('POST' === $arg_request->getMethod())
      {
        $form->bindRequest($arg_request);

        if ($form->isValid())
        {
          try
          {
            $this->_processContactRequest($form->getData());
          }
          catch (\Swift_Exception $exception)
          {
            $this->get('logger')->crit($exception->getMessage());

            throw new HttpException('Er is een fout opgetreden tijdens het versturen van uw bericht.', 500);
          }

          $this->get('session')->setFlash("notification", "Uw aanvraag is verzonden, wij nemen zo spoedig mogelijk contact met u op.");

          return new RedirectResponse($this->generateUrl('homepage'));
        }
      }

      $parameters = array('subscription' => $subscription
                         ,'form'         => $form->createView()
                         );

      return $this->render('DeBaasMediaContactBundle:Contact:form.html.twig', $parameters);
    }

    public function getEntityManager ()
    {
      return $this->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Process the contact request.
     *
     * @param   ContactRequest  $arg_contactRequest
     * 
     * @return  boolean
     */
    private function _processContactRequest (ContactRequest $arg_contactRequest)
    {
      $mailer  = $this->get('mailer');
      $message = \Swift_Message::newInstance();

      $message->setSubject(sprintf($this->container->getParameter('contact_request.subject'), $arg_contactRequest->name))
              ->setFrom($this->container->getParameter('contact_request.recipient.email_address'))
              ->setReplyTo($arg_contactRequest->emailAddress)
              ->setTo($this->container->getParameter('contact_request.recipient.email_address'), $this->container->getParameter('contact_request.recipient.name'))
              ->setBody($this->renderView('DeBaasMediaContactBundle:Email:recipient.txt.twig', array("contact" => $arg_contactRequest)));

      return $mailer->send($message);
    }

  }