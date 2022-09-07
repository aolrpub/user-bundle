<?php
namespace Aolr\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aolr_user');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('after_login_route')->end()
                ->scalarNode('aolr_pub_link')->end()
                ->scalarNode('support_email')->end()
                ->scalarNode('publisher_name')->defaultValue('Aolr')->end()
                ->scalarNode('privacy_url')->end()
                ->scalarNode('logo')->defaultValue('/images/logo.png')->end()
                ->scalarNode('manager_name')->defaultValue('default')->end()
                ->arrayNode('emails')
                    ->children()
                        ->scalarNode('from')->end()
                        ->scalarNode('transport')->end()
                        ->arrayNode('validation')
                            ->children()
                                ->scalarNode('subject')->end()
                                ->scalarNode('body')->end()
                            ->end()
                        ->end()
                        ->arrayNode('forgot_password')
                            ->children()
                                ->scalarNode('subject')->end()
                                ->scalarNode('body')->end()
                            ->end()
                        ->end()
                        ->arrayNode('verify_code')
                            ->children()
                                ->scalarNode('subject')->end()
                                ->scalarNode('body')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
