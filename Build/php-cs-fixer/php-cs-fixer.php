<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->setHeader('This file is part of the `sbuerk/typo3-ensure-admin` extension.');
$config->setFinder(
    (new PhpCsFixer\Finder())
        ->ignoreVCSIgnored(true)
        ->notPath('/^Build\/php-cs-fixer\/php-cs-fixer.php/')
        ->notPath('/^Build\/phpunit\/(UnitTestsBootstrap|FunctionalTestsBootstrap).php/')
        ->notName('/^ext_emconf.php/')
);
return $config;
