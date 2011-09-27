<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Controller;

  use DeBaasMedia\Bundle\ContactBundle\Form\ContactForm
    , DeBaasMedia\Bundle\ContactBundle\Model\ContactRequest
    , Symfony\Component\HttpKernel\Exception\HttpException
    , Symfony\Bundle\FrameworkBundle\Controller\Controller
    , AntiMattr\GoogleBundle\Maps\StaticMap
    , AntiMattr\GoogleBundle\Maps\Marker;

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

          return $this->redirect($this->generateUrl('homepage'));
        }
      }

      $map = new StaticMap();

      $map->setId("Schravenmade & Partners Advocaten");
      $map->setSize("316x316");
      $map->setZoom(13);

      $marker = new Marker();

      $marker->setLatitude(52.1353);
      $marker->setLongitude(5.0288);

      $map->addMarker($marker);

      $this->container->get('google.maps')->addMap($map);

      $article = $this->getEntityManager()
                      ->getRepository('DeBaasMedia\Bundle\ArticleBundle\Entity\Article')
                      ->findOneByUnifiedResourceNameAndNamespace('contact', 'extra');

      if (NULL === $article)
      {
        throw new NotFoundHttpException(sprintf('There is no Article for the urn: %s', $arg_unifiedResourceName));
      }

      $parameters = array('form'    => $form
                         ,'article' => $article
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

      $mailer->send($message);
    }

  }