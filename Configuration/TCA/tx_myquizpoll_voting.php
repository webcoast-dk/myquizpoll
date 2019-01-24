<?php
return array(
	'ctrl' => array (
		'title'     => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting',		
		'label'     => 'answer_no',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('myquizpoll').'icon_tx_myquizpoll_voting.gif',
	),
	'interface' => array(
			'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,ip,answer_no,question_id,foreign_val'
	),
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'renderType' => 'selectSingle',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'renderType' => 'selectSingle',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_myquizpoll_voting',
				'foreign_table_where' => 'AND tx_myquizpoll_voting.pid=###CURRENT_PID### AND tx_myquizpoll_voting.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'ip' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '20',	
				'eval' => 'trim',
			)
		),
		'question_id' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.question_id',	
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_myquizpoll_question',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'foreign_val' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.foreign_val',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255'
			)
		),
		'answer_no' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.answer_no',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '100',
					'lower' => '0'
				),
				'default' => 0
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, ip,question_id,foreign_val,answer_no')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);