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
    public function __construct(private readonly RequestMatcherInterface $urlMatcher)
    {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $match = $this->urlMatcher->matchRequest($request);
            $url = $this->determineTargetUrl($request, $match['targets']);

            $response = new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
            $response->setPublic();
            $response->setMaxAge(86400); // cache for a day
        } catch (\Exception) {
            $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    private function determineTargetUrl(Request $request, array $targets): string
    {
        if (1 === \count($targets)) {
            return reset($targets);
        }

        if (($lang = $request->query->get('lang')) && isset($targets[$lang])) {
            return $targets[$lang];
        }

        $desiredLanguage = $request->getPreferredLanguage(array_keys($targets));

        return $targets[$desiredLanguage] ?? reset($targets);
    }
}
