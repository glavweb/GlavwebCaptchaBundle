<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IsTrue
 *
 * @package Glavweb\CaptchaBundle\Validator\Constraints
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class IsTrue extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is not a valid captcha.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'glavweb_captcha.true';
    }
}
