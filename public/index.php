<?php

declare(strict_types=1);

/*
 * (c) Yanick Witschi
 *
 * @license MIT
 */

use Contao\ToContaoOrg\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

include_once __DIR__.'./../vendor/autoload.php';

$compiledRoutes = @include_once __DIR__.'/../var/dumped_routes.php';

if (false === $compiledRoutes) {
    throw new RuntimeException('You have to dump the routes first!');
}

$request = Request::createFromGlobals();
$requestContext = new RequestContext();
$requestContext->fromRequest($request);
$urlMatcher = new CompiledUrlMatcher($compiledRoutes, $requestContext);
$controller = new Controller($urlMatcher);
$response = $controller($request);
$response->send();
