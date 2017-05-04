<?php

/*
 * This file is part of the "GlavwebCaptchaBundle" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\CaptchaBundle\Controller;

use Glavweb\CaptchaBundle\Entity\CaptchaRequest;
use Glavweb\CaptchaBundle\Repository\CaptchaRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generates a captcha via a URL
 *
 * @package Glavweb\CaptchaBundle\Controller
 * @author Andrey Nilov <nilov@glavweb.ru>
 * @author Jeremy Livingston <jeremy.j.livingston@gmail.com>
 */
class CaptchaController extends Controller
{
    /**
     * @var array
     */
    private $allowedOptionsFromRequest = [
        'width',
        'height',
        'text_color',
        'background_color',
        'background_images',
        'max_front_lines',
        'max_behind_lines',
        'interpolation',
        'invalid_message'
    ];

    /**
     * Action that is used to generate the captcha, save its code, and stream the image
     *
     * @param string $token
     * @param Request $request
     * @return Response
     */
    public function generateCaptchaAction(string $token, Request $request): Response
    {
        $defaultOptions = $this->getParameter('glavweb_captcha.config');

        $options = $this->getFilteredRequestOptions($request);
        $options = array_merge($defaultOptions, $options);

        return $this->doGenerateCaptchaAction($token, $options);
    }

    /**
     * @param string $token
     * @param array $options
     * @return Response
     */
    protected function doGenerateCaptchaAction(string $token, array $options): Response
    {
        $generator = $this->get('glavweb_captcha.generator');

        $phrase = $generator->getPhrase($options);
        $generator->setPhrase($phrase);

        if (!$this->checkCaptchaRequest($token)) {
            return $this->errorResponse($options);
        }

        // Remove expired requests
        $this->deleteExpiredRequests();

        // save request in DB
        $this->saveCaptchaRequest($token, $phrase);

        $response = new Response($generator->generate($options));
        $response->headers->set('Content-type', 'image/jpeg');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * @param string $token
     * @return bool
     */
    private function checkCaptchaRequest(string $token): bool
    {
        /** @var CaptchaRequestRepository $captchaRequestRepository */
        $captchaRequestRepository = $this->getDoctrine()->getRepository(CaptchaRequest::class);

        $isExists = (bool)$captchaRequestRepository->findOneBy([
            'token' => $token
        ]);

        return !$isExists;
    }

    /**
     * @return mixed
     */
    private function deleteExpiredRequests()
    {
        /** @var CaptchaRequestRepository $captchaRequestRepository */
        $captchaRequestRepository = $this->getDoctrine()->getRepository(CaptchaRequest::class);

        return $captchaRequestRepository->deleteExpired();
    }

    /**
     * @param string $token
     * @param string $phrase
     */
    private function saveCaptchaRequest(string $token, string $phrase): void
    {
        $em = $this->getDoctrine()->getManager();
        $captchaRequest = new CaptchaRequest();
        $captchaRequest->setToken($token);
        $captchaRequest->setPhrase($phrase);

        $em->persist($captchaRequest);
        $em->flush();
    }

    /**
     * Returns an empty image with status code 428 Precondition Required
     *
     * @param array $options
     * @return Response
     */
    protected function errorResponse($options)
    {
        $generator = $this->get('glavweb_captcha.generator');
        $generator->setPhrase('');

        $response = new Response($generator->generate($options));
        $response->setStatusCode(428);
        $response->headers->set('Content-type', 'image/jpeg');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getFilteredRequestOptions(Request $request): array
    {
        $options = array_intersect_key($request->query->all(), array_flip($this->allowedOptionsFromRequest));
        return $options;
    }
}
