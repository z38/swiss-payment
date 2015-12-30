<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return Symfony\CS\Config\Config::create()
    ->fixers(array('-phpdoc_short_description', '-empty_return', '-pre_increment', 'ordered_use'))
    ->finder($finder)
;
