<?php

declare(strict_types=1);

/*
 * (c) Yanick Witschi
 *
 * @license MIT
 */

namespace Contao\ToContaoOrg;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class Controller
{
    private RequestMatcherInterface $urlMatcher;

    public function __construct(RequestMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $match = $this->urlMatcher->matchRequest($request);
            $targets = $match['targets'];
            $locales = array_keys($targets);

            if (1 === \count($locales)) {
                $url = reset($targets);
            } else {
                if ($lang = $request->query->get('lang')) {
                    $desiredLanguage = $lang;
                } else {
                    $desiredLanguage = $request->getPreferredLanguage($locales);
                }

                if (isset($targets[$desiredLanguage])) {
                    $url = $targets[$desiredLanguage];
                } else {
                    $url = reset($targets);
                }
            }

            $response = new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
            $response->setPublic();
            $response->setMaxAge(86400); // cache for a day
        } catch (\Exception $e) {
            $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}
