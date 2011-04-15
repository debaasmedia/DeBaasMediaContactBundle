<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Form;

  use Symfony\Component\Form\Form,
      Symfony\Component\Form\TextField,
      Symfony\Component\Form\TextareaField;

  /**
   * ContactForm.
   *
   * @author  Marijn Huizendveld <marijn.huizendveld@gmail.com>
   */
  class ContactForm extends Form
  {

    /**
     * Configure the form
     *
     * @return  void
     */
    protected function configure ()
    {
      $this->add(new TextField('name'));
      $this->add(new TextField('emailAddress'));
      $this->add(new TextareaField('message'));

      $this->setDataClass('DeBaasMedia\Bundle\ContactBundle\Model\ContactRequest');
    }

  }