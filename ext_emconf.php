<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Bookmarks',
    'description' => 'Bookmark everything',
    'category' => 'plugin',
    'author' => 'Carsten Walther',
    'author_email' => 'walther.carsten@web.de',
    'state' => 'stable',
    'internal' => false,
    'uploadfolder' => false,
    'createDirs' => false,
    'clearCacheOnLoad' => false,
    'version' => '9.5.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5'
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
