<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('build')
    ->exclude('Resources')
    ->exclude('Tests')
    ->exclude('vendor')
    ->in(__DIR__)
;

return new Sami($iterator, array(
    'title'                => 'blocking API',
    'build_dir'            => __DIR__.'/build/apidocs',
    'cache_dir'            => __DIR__.'/build/cache',
    'default_opened_level' => 2,
));
