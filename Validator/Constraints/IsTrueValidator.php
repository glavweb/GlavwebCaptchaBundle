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

use Doctrine\Bundle\DoctrineBundle\Registry;
use Glavweb\CaptchaBundle\Entity\CaptchaRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsTrueValidator
 *
 * @package Glavweb\CaptchaBundle\Validator\Constraints
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class IsTrueValidator extends ConstraintValidator
{
    /**
     * Enable captcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * Construct.
     *
     * @param Boolean $enabled
     * @param RequestStack $requestStack
     * @param Registry $doctrine
     */
    public function __construct($enabled, Registry $doctrine, RequestStack $requestStack)
    {
        $this->enabled      = $enabled;
        $this->doctrine     = $doctrine;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // if captcha is disabled, always valid
        if (!$this->enabled) {
            return;
        }

        $token  = $this->requestStack->getMasterRequest()->get('captcha_token', '');
        $phrase = $this->requestStack->getMasterRequest()->get('captcha_phrase', '');

        $isValid = $this->checkCaptcha($token, $phrase);
        if (!$isValid) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param string $token
     * @param string $phrase
     * @return bool
     */
    private function checkCaptcha(string $token, string $phrase): bool
    {
        if (!$token || !$phrase) {
            return false;
        }

        /** @var CaptchaRequest $captchaRequest */
        $captchaRequest = $this->doctrine->getRepository(CaptchaRequest::class)->findOneBy([
            'token' => $token
        ]);

        $isValid =
            $captchaRequest->getExpiredAt() > new \DateTime() &&
            $captchaRequest->getPhrase() == $phrase
        ;

        return $isValid;
    }
}
