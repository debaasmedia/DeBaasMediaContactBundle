<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Controller;

  use DeBaasMedia\Bundle\ContactBundle\Form\ContactForm
    , DeBaasMedia\Bundle\ContactBundle\Model\ContactRequest
    , Symfony\Component\HttpKernel\Exception\HttpException
    , Symfony\Bundle\FrameworkBundle\Controller\Controller;

  /**
   * ContactRequest.
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
      $form    = ContactForm::create($this->get('form.context'), 'contact');
      $request = $this->get('request');
  
      if ('POST' === $request->getMethod())
      {
        $form->bind($request, new ContactRequest());

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
          $this->redirect($this->generateUrl('homepage'));
        }
      }

      $parameters = array('form' => $form);

      return $this->render('DeBaasMediaContactBundle:Contact:form.html.twig', $parameters);
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
      $message = \Swift_Message::newInstance();

      $message->setSubject(sprintf($this->container->getParameter('contact_request.subject'), $arg_contactRequest->name))
              ->setFrom($arg_contactRequest->emailAddress)
              ->setTo($this->container->getParameter('contact_request.recipient.email_address'), $this->container->getParameter('contact_request.recipient.name'))
              ->setBody($this->renderView('DeBaasMediaContactBundle:Email:recipient.txt.twig', array("contact" => $arg_contactRequest)));

      $this->get('mailer')->send($message);
    }

  }