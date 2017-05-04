<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @package Glavweb\CaptchaBundle\DependencyInjection
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('glavweb_captcha');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('length')->defaultValue(5)->end()
                ->scalarNode('width')->defaultValue(130)->end()
                ->scalarNode('height')->defaultValue(50)->end()
                ->scalarNode('font')->defaultValue(__DIR__.'/../Generator/Font/captcha.ttf')->end()
                ->scalarNode('keep_value')->defaultValue(false)->end()
                ->scalarNode('charset')->defaultValue('abcdefhjkmnprstuvwxyz23456789')->end()
                ->scalarNode('as_file')->defaultValue(false)->end()
                ->scalarNode('as_url')->defaultValue(false)->end()
                ->scalarNode('reload')->defaultValue(false)->end()
                ->scalarNode('image_folder')->defaultValue('captcha')->end()
                ->scalarNode('web_path')->defaultValue('%kernel.root_dir%/../web')->end()
                ->scalarNode('gc_freq')->defaultValue(100)->end()
                ->scalarNode('expiration')->defaultValue(60)->end()
                ->scalarNode('quality')->defaultValue(30)->end()
                ->scalarNode('invalid_message')->defaultValue('Bad code value')->end()
                ->scalarNode('bypass_code')->defaultValue(null)->end()
                ->scalarNode('humanity')->defaultValue(0)->end()
                ->scalarNode('distortion')->defaultValue(true)->end()
                ->scalarNode('max_front_lines')->defaultValue(null)->end()
                ->scalarNode('max_behind_lines')->defaultValue(null)->end()
                ->scalarNode('interpolation')->defaultValue(true)->end()
                ->arrayNode('text_color')->prototype('scalar')->end()->end()
                ->arrayNode('background_color')->prototype('scalar')->end()->end()
                ->arrayNode('background_images')->prototype('scalar')->end()->end()
                ->scalarNode('disabled')->defaultValue(false)->end()
                ->scalarNode('ignore_all_effects')->defaultValue(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
