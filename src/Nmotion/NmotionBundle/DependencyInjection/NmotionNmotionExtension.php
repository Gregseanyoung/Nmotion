<?php

namespace Nmotion\NmotionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NmotionNmotionExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $alias = $this->getAlias();
        $container->setParameter($alias . '.facebook.app_id', $config['facebook']['app_id'] ?: '');
        $container->setParameter($alias . '.facebook.secret', $config['facebook']['secret'] ?: '');
        $container->setParameter($alias . '.facebook.api.class', 'Nmotion\NmotionBundle\Facebook\Facebook');

        if (isset($config['upload']['root_dir'])) {
            $container->setParameter($alias . '.upload.root_dir', $config['upload']['root_dir'] ?: '');
        }
        if (isset($config['upload']['root_web'])) {
            $container->setParameter($alias . '.upload.root_web', $config['upload']['root_web'] ?: '');
        }
    }
}
