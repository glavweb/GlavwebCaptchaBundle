<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CaptchaRequestRepository
 *
 * @package Glavweb\CaptchaBundle\Repository
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class CaptchaRequestRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function deleteExpired()
    {
        $qb = $this->createQueryBuilder("t")
            ->delete()
            ->where('t.expiredAt < :current_time')
            ->setParameter('current_time', new \DateTime())
        ;

        return $qb->getQuery()->execute();
    }

}