<?php
return array(
	'ctrl' => array (
		'title'     => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result',		
		'label'     => 'name',	
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
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('myquizpoll').'icon_tx_myquizpoll_result.gif',
	),
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,name,email,homepage,ip,p_or_a,p_max,percent,o_max,o_percent,qids,cids,fids,sids,joker1,joker2,joker3,firsttime,lasttime,lastcat,nextcat,fe_uid,start_uid'
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
				'foreign_table'       => 'tx_myquizpoll_result',
				'foreign_table_where' => 'AND tx_myquizpoll_result.pid=###CURRENT_PID### AND tx_myquizpoll_result.sys_language_uid IN (-1,0)',
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
		'name' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'email' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'homepage' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.homepage',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'ip' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'p_or_a' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.p_or_a',		
			'config' => array (
				'type'     => 'input',
				'size'     => '7',
				'max'      => '8',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'p_max' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.p_max',		
			'config' => array (
				'type'     => 'input',
				'size'     => '7',
				'max'      => '8',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'percent' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.percent',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'o_max' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.o_max',		
			'config' => array (
				'type'     => 'input',
				'size'     => '7',
				'max'      => '8',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'o_percent' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.o_percent',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'qids' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.qids',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_myquizpoll_question',	
				'size' => 4,	
				'minitems' => 0,
				'maxitems' => 200,
			)
		),
		'cids' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.cids',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_myquizpoll_question',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 200,
			)
		),
		'fids' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.fids',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_myquizpoll_question',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 200,
			)
		),
		'sids' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.sids',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_myquizpoll_question',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
		'joker1' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker1',		
			'config' => array (
				'type' => 'check',
			)
		),
		'joker2' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker2',		
			'config' => array (
				'type' => 'check',
			)
		),
		'joker3' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker3',		
			'config' => array (
				'type' => 'check',
			)
		),
		'firsttime' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.firsttime',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'lasttime' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.lasttime',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'lastcat' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.lastcat',		
			'config' => array (
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_myquizpoll_category',	
				'foreign_table_where' => 'ORDER BY tx_myquizpoll_category.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'nextcat' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.nextcat',		
			'config' => array (
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_myquizpoll_category',	
				'foreign_table_where' => 'ORDER BY tx_myquizpoll_category.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'fe_uid' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.fe_uid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'start_uid' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.start_uid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'pages',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name;;;richtext[]:rte_transform[mode=ts], email, homepage, ip, p_or_a;;2, qids;;3, cids, fids, sids, firsttime, lasttime, lastcat, nextcat, fe_uid, start_uid')
	),
	'palettes' => array (
		'1' => array('showitem' => ''),
		"2" => array("showitem" => "p_max, percent, o_max, o_percent"),
		"3" => array("showitem" => "joker1, joker2, joker3")
	)
);