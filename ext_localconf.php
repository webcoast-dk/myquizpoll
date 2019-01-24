<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_myquizpoll_question=1
');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_myquizpoll_category=1
');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_myquizpoll_pi1.php', '_pi1', 'list_type', 0);

$TYPO3_CONF_VARS['FE']['eID_include']['myquizpoll_eID'] = 'EXT:myquizpoll/pi1/poll_eID.php';
$TYPO3_CONF_VARS['FE']['eID_include']['myquiz_eID'] = 'EXT:myquizpoll/pi1/quiz_eID.php';
$TYPO3_CONF_VARS['FE']['eID_include']['starrating'] = 'EXT:myquizpoll/pi1/class.tx_myquizpoll_eidstarrating.php';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_myquizpoll_question = < plugin.'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getCN($_EXTKEY).'_pi1
	tt_content.shortcut.20.0.conf.tx_myquizpoll_question.CMD = singleView
',43);


if (TYPO3_MODE === 'BE') {
	/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
	$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
	$iconRegistry->registerIcon(
			'ext-myquizpoll-wizard-icon',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:myquizpoll/pi1/ce_wiz.gif']
	);
	$iconRegistry->registerIcon(
			'ext-myquizpoll-folder-icon',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:myquizpoll/ext_icon_myquizpoll_folder.gif']
	);
}
?>