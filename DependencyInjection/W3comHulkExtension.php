<?php

namespace W3com\HulkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class W3comHulkExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('hulk.config', $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('table_provider.xml');
        $loader->load('display_controller.xml');
        $loader->load('create_view_controller.xml');
        $loader->load('update_project_entity_controller.xml');
        $loader->load('update_sap_controller.xml');
        $loader->load('menu_builder.xml');
        $loader->load('menu_session.xml');
        $loader->load('save_filters_controller.xml');
    }

}