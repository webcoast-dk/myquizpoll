<?php
defined('TYPO3_MODE') or die();

// Override icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'My Quiz and Poll',
    1 => 'myquizpoll',
    2 => 'ext-myquizpoll-folder-icon'
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-myquizpoll'] = 'ext-myquizpoll-folder-icon';