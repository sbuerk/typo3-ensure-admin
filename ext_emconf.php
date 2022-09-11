<?php

$EM_CONF['cli_ensure_admin'] = [
    'title' => 'TYPO3 Ensure Admin User',
    'description' => 'Provides a TYPO3 cli command to create or update admin user',
    'category' => 'misc',
    'version' => '2.0.2',
    'module' => '',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Stefan Bürk',
    'author_email' => 'stefan@buerk.tech',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
