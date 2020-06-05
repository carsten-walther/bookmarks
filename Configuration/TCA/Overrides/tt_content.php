<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Walther.Bookmarks',
    'bookmark',
    'LLL:EXT:bookmarks/Resources/Private/Language/locallang_be.xlf:general.title',
    'EXT:bookmarks/Resources/Public/Icons/ext-bookmarks-wizard-icon.svg'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['bookmarks_bookmark'] = 'select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['bookmarks_bookmark'] = 'bodytext';

$GLOBALS['TCA']['tt_content']['types']['list']['columnsOverrides']['bodytext']['config']['enableRichtext']['bookmarks_bookmark'] = 1;
$GLOBALS['TCA']['tt_content']['types']['list']['columnsOverrides']['bodytext']['config']['richtextConfiguration']['bookmarks_bookmark'] = 'default';
