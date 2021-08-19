<?php

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__.'/vendor/contao/easy-coding-standard/config/default.php');

    $services = $containerConfigurator->services();

    $services
        ->set(HeaderCommentFixer::class)
        ->call('configure', [[
            'header' => "(c) Yanick Witschi\n\n@license MIT",
        ]])
    ;
};
