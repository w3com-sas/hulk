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
        $loader->load('json_table.xml');
        $loader->load('create_view.xml');
        $loader->load('update_view.xml');
        $loader->load('update_entity.xml');
        $loader->load('json_file_manager.xml');
        $loader->load('admin.xml');
        $loader->load('session_manager.xml');

    }

}