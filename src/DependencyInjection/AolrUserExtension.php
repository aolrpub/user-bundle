<?php
namespace Aolr\UserBundle\DependencyInjection;

use Aolr\UserBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class AolrUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('service.yml');

        $configManagerDef = $container->getDefinition('Aolr\UserBundle\Service\ConfigManager');
        $configManagerDef->replaceArgument(0, $config);

        foreach ($container->getDefinitions() as $definition) {

            $definition->setBindings([
                '$em' => new BoundArgument(new Reference('doctrine.orm.' . $config['manager_name'] . '_entity_manager'), false)
            ]);
        }

    }

    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['TwigBundle'])) {
            $container->prependExtensionConfig('twig', [
                'globals' => [
                    'aolrUserConfig' => '@Aolr\UserBundle\Service\ConfigManager'
                ]
            ]);
        }
    }
}
