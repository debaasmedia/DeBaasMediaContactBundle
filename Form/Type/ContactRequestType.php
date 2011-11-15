<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Form\Type;

  use Symfony\Component\Form\AbstractType
    , Symfony\Component\Form\FormBuilder;

  /**
   * ContactRequestType.
   *
   * @author  Marijn Huizendveld <marijn@debaasmedia.nl>
   *
   * @copyright De Baas Media (2010 - 2011)
   */
  class ContactRequestType extends AbstractType
  {

    /**
     * Build the form.
     *
     * @param   Symfony\Component\Form\FormBuilder  $builder
     * @param   array                               $options
     *
     * @return  void
     */
    public function buildForm (FormBuilder $builder, array $options)
    {
      $builder->add('name')
              ->add('emailAddress', 'email')
              ->add('message', 'textarea');
    }

    /**
     * Get the default form option.
     *
     * @param   array $options
     *
     * @return  array
     */
    public function getDefaultOptions (array $options)
    {
      return array('data_class' => 'DeBaasMedia\Bundle\ContactBundle\Model\ContactRequest');
    }

    /**
     * Get the name of the type.
     *
     * @return  string
     */
    public function getName ()
    {
      return 'contact_request';
    }

  }