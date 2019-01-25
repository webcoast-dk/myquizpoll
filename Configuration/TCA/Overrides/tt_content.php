<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addPlugin([
    'LLL:EXT:myquizpoll/locallang_db.xml:tt_content.list_type_pi1',
    'myquizpoll_pi1',
    'EXT:myquizpoll/ext_icon.gif'
], 'list_type', 'myquizpoll');

// Einbindung Flexform
$pluginSignature = 'myquizpoll_pi1';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:myquizpoll/flexform.xml');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,recursive';
