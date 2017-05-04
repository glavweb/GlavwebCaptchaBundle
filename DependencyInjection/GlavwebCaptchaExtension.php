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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class GlavwebCaptchaExtension
 *
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @package Glavweb\CaptchaBundle\DependencyInjection
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class GlavwebCaptchaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('glavweb_captcha.config', $config);
        $container->setParameter('glavweb_captcha.enabled', $config['enabled']);
        $container->setParameter('glavweb_captcha.config.image_folder', $config['image_folder']);
        $container->setParameter('glavweb_captcha.config.web_path', $config['web_path']);
        $container->setParameter('glavweb_captcha.config.gc_freq', $config['gc_freq']);
        $container->setParameter('glavweb_captcha.config.expiration', $config['expiration']);
    }
}
