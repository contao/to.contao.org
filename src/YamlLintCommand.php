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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'app:yaml-lint',
    description: 'Lint a yaml file.',
)]
class YamlLintCommand extends Command
{
    public function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The path to the file');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            Yaml::parseFile($input->getArgument('file'));
        } catch (ParseException $e) {
            $output->writeln('The yaml file is invalid: '.$e->getMessage());

            return 1;
        }

        $output->writeln('Shiny!');

        return 0;
    }
}
