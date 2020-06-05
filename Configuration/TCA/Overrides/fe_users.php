<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', [
    'bookmarks' => [
        'exclude' => true,
        'label' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:fe_users.bookmarks',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_bookmarks_domain_model_bookmark',
            'foreign_field' => 'feuser',
            'maxitems' => 9999,
            'appearance' => [
                'collapseAll' => true,
                'expandSingle' => true,
            ],
        ]
    ]
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:fe_users.bookmarks.tab, bookmarks');
