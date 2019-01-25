<?php
return array(
	'ctrl' => array (
		'title'     => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation',
		'label'     => 'uid',
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
		'iconfile'          => 'EXT:myquizpoll/icon_tx_myquizpoll_relation.gif',
	),
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,user_id,question_id,textinput,checked1,checked2,checked3,checked4,checked5,checked6,points,nextcat'
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
				'foreign_table'       => 'tx_myquizpoll_relation',
				'foreign_table_where' => 'AND tx_myquizpoll_relation.pid=###CURRENT_PID### AND tx_myquizpoll_relation.sys_language_uid IN (-1,0)',
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
		'user_id' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.user_id',
			'config' => array (
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_myquizpoll_result',
				'foreign_table_where' => 'AND tx_myquizpoll_result.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_result.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'question_id' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.question_id',
			'config' => array (
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_myquizpoll_question',
				'foreign_table_where' => 'AND tx_myquizpoll_question.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_question.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'textinput' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.textinput',
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '3',
			)
		),
		'checked1' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked1',
			'config' => array (
				'type' => 'check',
			)
		),
		'checked2' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked2',
			'config' => array (
				'type' => 'check',
			)
		),
		'checked3' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked3',
			'config' => array (
				'type' => 'check',
			)
		),
		'checked4' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked4',
			'config' => array (
				'type' => 'check',
			)
		),
		'checked5' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked5',
			'config' => array (
				'type' => 'check',
			)
		),
		'checked6' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked6',
			'config' => array (
				'type' => 'check',
			)
		),
		'points' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.points',
			'config' => array (
				'type'     => 'input',
				'size'     => '7',
				'max'      => '8',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
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
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, user_id, question_id, textinput, checked1, checked2, checked3, checked4, checked5, checked6, points, nextcat')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
