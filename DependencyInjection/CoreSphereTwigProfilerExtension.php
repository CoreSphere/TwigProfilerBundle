<?php

namespace CoreSphere\TwigProfilerBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class CoreSphereTwigProfilerExtension extends Extension
{
    /**
     * Yaml config files to load
     * @var array
     */
    protected $resources = array(
        'services' => 'services.yml',
    );

    /**
     * Loads the services based on your application configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = $this->getFileLoader($container);
        $loader->load($this->resources['services']);

    }

    /**
     * Get File Loader
     *
     * @param ContainerBuilder $container
     */
    public function getFileLoader($container)
    {
        return new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    }
}