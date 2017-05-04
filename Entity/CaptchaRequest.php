<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class CaptchaRequest
 *
 * @package Glavweb\CaptchaBundle\Entity
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="glavweb_captcha_requests")
 * @ORM\Entity(repositoryClass="Glavweb\CaptchaBundle\Repository\CaptchaRequestRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"token"})
 */
class CaptchaRequest
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Captcha request ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", nullable=true, unique=true, options={"comment": "Token"})
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="phrase", type="string", options={"comment": "Phrase"})
     */
    private $phrase;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment": "Date time when entity was created"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expired_at", type="datetime", options={"comment": "Date time when entity will be expired"})
     */
    private $expiredAt;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $currentTime = new \DateTime();
        $expiredAt = $currentTime->modify('+12 hours');

        $this->setCreatedAt($currentTime);
        $this->setExpiredAt($expiredAt);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return CaptchaRequest
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set phrase
     *
     * @param string $phrase
     *
     * @return CaptchaRequest
     */
    public function setPhrase($phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get phrase
     *
     * @return string
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CaptchaRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set expiredAt
     *
     * @param \DateTime $expiredAt
     *
     * @return CaptchaRequest
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get expiredAt
     *
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }
}
