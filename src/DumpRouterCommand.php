<?php

declare(strict_types=1);

/*
 * (c) Yanick Witschi
 *
 * @license MIT
 */

namespace Contao\ToContaoOrg;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'app:dump-router',
    description: 'Dump the router file.',
)]
class DumpRouterCommand extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $routes = new RouteCollection();
        $i = 0;
        $routesData = Yaml::parseFile(__DIR__.'/../redirects.yaml');

        foreach ($routesData as $shortLink => $targets) {
            $route = new Route('/'.$shortLink, ['targets' => (array) $targets], [], ['utf8' => true]);

            $routes->add('generated_'.++$i, $route);
        }

        $dumper = new CompiledUrlMatcherDumper($routes);

        $fs = new Filesystem();
        $fs->dumpFile(__DIR__.'/../var/dumped_routes.php', $dumper->dump());

        return 0;
    }
}
