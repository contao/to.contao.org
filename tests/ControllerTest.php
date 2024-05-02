<?php

declare(strict_types=1);

/*
 * (c) Yanick Witschi
 *
 * @license MIT
 */

namespace Contao\ToContaoOrg\Test;

use Contao\ToContaoOrg\Controller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class ControllerTest extends TestCase
{
    public function testControllerWithNonExistingShortLink(): void
    {
        $request = Request::create('/contao');

        $urlMatcher = $this->createMock(RequestMatcherInterface::class);
        $urlMatcher
            ->expects($this->once())
            ->method('matchRequest')
            ->with($request)
            ->willThrowException(new ResourceNotFoundException())
        ;

        $controller = new Controller($urlMatcher);
        $response = $controller($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @dataProvider existingShortLinkProvider
     */
    public function testControllerWithExistingShortLink(Request $request, array $routeMatch, string $expectedTargetUrl): void
    {
        $urlMatcher = $this->createMock(RequestMatcherInterface::class);
        $urlMatcher
            ->expects($this->once())
            ->method('matchRequest')
            ->with($request)
            ->willReturn($routeMatch)
        ;

        $controller = new Controller($urlMatcher);
        $response = $controller($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(Response::HTTP_TEMPORARY_REDIRECT, $response->getStatusCode());
        $this->assertSame(86400, $response->getMaxAge());
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertSame($expectedTargetUrl, $response->headers->get('Location'));
    }

    public static function existingShortLinkProvider(): iterable
    {
        yield 'Test correct match if only one target is configured' => [
            Request::create('/contao'),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                ],
            ],
            'https://contao.org/en/',
        ];

        yield 'Test fallback if no preferred language was specified' => [
            Request::create('/contao'),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/en/',
        ];

        yield 'Test correct fallback if Accept-Language contains a language that does not exist' => [
            Request::create('/contao', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'zh;q=0.7,fr;q=0.3']),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/en/',
        ];

        yield 'Test correct match if Accept-Language contains "en"' => [
            Request::create('/contao', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'en;q=0.7,de;q=0.3']),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/en/',
        ];

        yield 'Test correct match if Accept-Language contains "de"' => [
            Request::create('/contao', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'de;q=0.7,en;q=0.3']),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/de/',
        ];

        yield 'Test correct match if Accept-Language contains "de-CH"' => [
            Request::create('/contao', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'de-CH;q=0.7,en;q=0.3']),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/de/',
        ];

        yield 'Test correct match if Accept-Language contains "de" but "en" was forced' => [
            Request::create('/contao?lang=en', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'de;q=0.7,en;q=0.3']),
            [
                'targets' => [
                    'en' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/en/',
        ];

        yield 'Test correct match if Accept-Language contains "de" and unknown was forced' => [
            Request::create('/contao?lang=en', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'de;q=0.7,en;q=0.3']),
            [
                'targets' => [
                    'fr' => 'https://contao.org/en/',
                    'de' => 'https://contao.org/de/',
                ],
            ],
            'https://contao.org/de/',
        ];
    }
}
