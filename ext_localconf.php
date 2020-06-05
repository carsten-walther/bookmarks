<?php

defined('TYPO3_MODE') || die('Access denied.');

// Include new content elements to modWizards
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bookmarks/Configuration/TSconfig/Page/Mod/Wizards/NewContentElement.typoscript">');

// Register Icons
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)->registerIcon(
    'ext-bookmarks-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:bookmarks/Resources/Public/Icons/ext-bookmarks-wizard-icon.svg']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin (
    'Walther.Bookmarks',
    'Bookmark',
    ['Bookmark' => 'index', 'Ajax' => 'list,create,delete'],
    ['Bookmark' => 'index', 'Ajax' => 'list,create,delete']
);

// Register ViewHelper
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['bookmark'] = ['Walther\Bookmarks\ViewHelpers'];
