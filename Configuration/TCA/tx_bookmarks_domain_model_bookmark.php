<?php

defined('TYPO3_MODE') || die('Access denied.');

$GLOBALS['TCA']['tx_bookmarks_domain_model_bookmark'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark',
        'label' => 'feuser',
        'label_alt' => 'parent_uid,parent_pid,parent_table',
        'label_alt_force' => true,
        'iconfile' => 'EXT:bookmarks/Resources/Public/Icons/tx_bookmarks_domain_model_bookmark.svg',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideAtCopy' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'hideTable' => false,
    ],
    'interface' => [
        'showRecordFieldList' => 'feuser,parent_pid,parent_table,parent_uid'
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid,feuser,--palette--;LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark.parent;parent'
        ]
    ],
    'palettes' => [
        'parent' => [
            'showitem' => 'parent_uid,parent_pid,parent_table'
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'default' => 0,
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'default' => 0,
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => 'tx_bookmarks_domain_model_bookmark',
                'foreign_table_where' => 'AND tx_bookmarks_domain_model_bookmark.sys_language_uid IN (-1,0)'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check'
            ],
        ],
        'parent_pid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark.parent_pid',
            'config' => [
                'type' => 'input',
                'size' => 6,
                'max' => 32,
                'eval' => 'int,trim,required'
            ]
        ],
        'parent_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark.parent_uid',
            'config' => [
                'type' => 'input',
                'size' => 6,
                'max' => 32,
                'eval' => 'int,trim,required'
            ]
        ],
        'parent_table' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark.parent_table',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'max' => 128,
                'eval' => 'trim,required'
            ]
        ],
        'feuser' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bookmarks/Resources/Private/Language/locallang_db.xlf:tx_bookmarks_domain_model_bookmark.feuser',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', ''],
                ],
                'foreign_table' => 'fe_users',
                'eval' => 'required',
                'default' => ''
            ]
        ],
    ]
];
