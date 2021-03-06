<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\Generator;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Symfony\Component\Routing\RouterInterface;
use Gregwar\Captcha\CaptchaBuilderInterface;
use Gregwar\Captcha\PhraseBuilderInterface;

/**
 * Uses configuration parameters to call the services that generate captcha images
 *
 * @author Gregwar <g.passault@gmail.com>
 * @author Jeremy Livingston <jeremy.j.livingston@gmail.com>
 */
class CaptchaGenerator
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var CaptchaBuilder
     */
    protected $builder;

    /**
     * @var PhraseBuilder
     */
    protected $phraseBuilder;

    /**
     * @var ImageFileHandler
     */
    protected $imageFileHandler;

    /**
     * @param RouterInterface $router
     * @param CaptchaBuilderInterface $builder
     * @param PhraseBuilderInterface $phraseBuilder
     * @param ImageFileHandler $imageFileHandler
     */
    public function __construct(
        RouterInterface $router,
        CaptchaBuilderInterface $builder,
        PhraseBuilderInterface $phraseBuilder,
        ImageFileHandler $imageFileHandler
    ) {
        $this->router           = $router;
        $this->builder          = $builder;
        $this->phraseBuilder    = $phraseBuilder;
        $this->imageFileHandler = $imageFileHandler;    
    }

    /**
     * Get the captcha URL, stream, or filename that will go in the image's src attribute
     *
     * @param array $options
     * @return string
     */
    public function getCaptchaCode(array &$options)
    {
        $this->builder->setPhrase($this->getPhrase($options));

        // Randomly execute garbage collection and returns the image filename
        if ($options['as_file']) {
            $this->imageFileHandler->collectGarbage();

            return $this->generate($options);
        }

        // Returns the image generation URL
        if ($options['as_url']) {
            return $this->router->generate('gregwar_captcha.generate_captcha', 
                array('key' => $options['session_key'], 'n' => md5(microtime(true).mt_rand())));
        }

        return 'data:image/jpeg;base64,' . base64_encode($this->generate($options));
    }

    /**
     * Sets the phrase to the builder
     */
    public function setPhrase($phrase)
    {
        $this->builder->setPhrase($phrase);
    }

    /**
     * @param array $options
     * @return string
     */
    public function generate(array &$options)
    {
        $this->builder->setDistortion($options['distortion']);

        $this->builder->setMaxFrontLines($options['max_front_lines']);
        $this->builder->setMaxBehindLines($options['max_behind_lines']);

        if (isset($options['text_color']) && $options['text_color']) {
            if (count($options['text_color']) !== 3) {
                throw new \RuntimeException('text_color should be an array of r, g and b');
            }

            $color = $options['text_color'];
            $this->builder->setTextColor($color[0], $color[1], $color[2]);
        }

        if (isset($options['background_color']) && $options['background_color']) {
            if (count($options['background_color']) !== 3) {
                throw new \RuntimeException('background_color should be an array of r, g and b');
            }

            $color = $options['background_color'];
            $this->builder->setBackgroundColor($color[0], $color[1], $color[2]);
        }

        $this->builder->setInterpolation($options['interpolation']);

        $fingerprint = isset($options['fingerprint']) ? $options['fingerprint'] : null;

        $this->builder->setBackgroundImages($options['background_images']);
        $this->builder->setIgnoreAllEffects($options['ignore_all_effects']);

        $content = $this->builder->build(
            $options['width'],
            $options['height'],
            $options['font'],
            $fingerprint
        )->getGd();

        if ($options['keep_value']) {
            $options['fingerprint'] = $this->builder->getFingerprint();
        }

        if (!$options['as_file']) {
            ob_start();
            imagejpeg($content, null, $options['quality']);

            return ob_get_clean();
        }

        return $this->imageFileHandler->saveAsFile($content);
    }

    /**
     * @param array $options
     * @return string
     */
    public function getPhrase(array &$options)
    {
        // Get the phrase that we'll use for this image
        if ($options['keep_value'] && isset($options['phrase'])) {
            $phrase = $options['phrase'];
        } else {
            $phrase = $this->phraseBuilder->build($options['length'], $options['charset']);
            $options['phrase'] = $phrase;
        }
        
        return $phrase;
    }
}
