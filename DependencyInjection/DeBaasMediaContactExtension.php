<?php

  namespace DeBaasMedia\Bundle\ContactBundle\DependencyInjection;

  use Symfony\Component\Config\FileLocator
    , Symfony\Component\Config\Definition\Processor
    , Symfony\Component\Config\Definition\Builder\TreeBuilder
    , Symfony\Component\DependencyInjection\Loader\XmlFileLoader
    , Symfony\Component\DependencyInjection\ContainerBuilder
    , Symfony\Component\HttpKernel\DependencyInjection\Extension;

  /**
   * DeBaasMediaContactExtension.
   *
   * @author  Marijn Huizendveld <marijn.huizendveld@gmail.com>
   */
  class DeBaasMediaContactExtension extends Extension
  {

    /**
     * {@inheritDoc}
     */
    public function load (array $configs, ContainerBuilder $container)
    {
      $processor = new Processor();

      $config = $processor->process($this->generateConfigTree(new TreeBuilder), $configs);

      $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__ . '/../Resources/config')));

      $loader->load('contact.xml');

      $container->setParameter('contact_request.recipient.email_address', $config['email_address']);
      $container->setParameter('contact_request.recipient.name', $config['name']);
      $container->setParameter('contact_request.subject', $config['subject']);
    }

    /**
     * Generates the configuration tree.
     *
     * @return Symfony\Component\Config\Definition\NodeInterface
     */
    public function generateConfigTree (TreeBuilder $arg_builder)
    {
      $arg_builder->root('de_baas_media_contact')
                    ->children()
                      ->scalarNode('email_address')->end()
                      ->scalarNode('subject')->defaultValue('[website] Contact aanvraag van %s')->end()
                      ->scalarNode('name')->defaultValue(NULL)->end()
                    ->end()
                  ->end();

      return $arg_builder->buildTree();
    }

  }