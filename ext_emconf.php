<?php

$EM_CONF['sbuerk_ensureadmin'] = [
    'title' => 'TYPO3 Ensure Admin User',
    'description' => 'Provides a TYPO3 cli command to create or update admin user',
    'category' => 'misc',
    'version' => '0.0.0',
    'module' => '',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Stefan Bürk',
    'author_email' => 'stefan@buerk.tech',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
