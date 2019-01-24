<?php
return array(
		'ctrl' => array (
				'title'     => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question',
				'label'     => 'title',
				'tstamp'    => 'tstamp',
				'crdate'    => 'crdate',
				'cruser_id' => 'cruser_id',
				'versioningWS' => TRUE,
				'origUid' => 't3_origuid',
				'languageField'            => 'sys_language_uid',
				'transOrigPointerField'    => 'l10n_parent',
				'transOrigDiffSourceField' => 'l10n_diffsource',
				'sortby' => 'sorting',
				'delete' => 'deleted',
				'enablecolumns' => array (
						'disabled' => 'hidden',
						'fe_group' => 'fe_group',
				),
				'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('myquizpoll').'icon_tx_myquizpoll_question.gif',
		),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,fe_group,title,title_hide,name,qtype,category,answer1,correct1,points1,joker1_1,joker2_1,category1,answer2,correct2,points2,joker1_2,joker2_2,category2,answer3,correct3,points3,joker1_3,joker2_3,category3,answer4,correct4,points4,joker1_4,joker2_4,category4,answer5,correct5,points5,joker1_5,joker2_5,category5,answer6,correct6,points6,joker1_6,joker2_6,category6,explanation,joker3,points,category_next,image,alt_text'
	),
	'types' => array(
			'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;2;;2-2-2, name;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];3-3-3, image;;18, qtype;;3, answer1;;4, correct1;;5, answer2;;6, correct2;;7, answer3;;8, correct3;;9, answer4;;10, correct4;;11, answer5;;12, correct5;;13, answer6;;14, correct6;;15, points;;16, explanation;;17;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]')
	),
	'palettes' => array(
		'1' => array('showitem' => 'fe_group'),
		'2' => array('showitem' => 'title_hide'),
		'3' => array('showitem' => 'category'),
		"4" => array("showitem" => "category1"),
		"5" => array("showitem" => "points1, joker1_1, joker2_1"),
		"6" => array("showitem" => "category2"),
		"7" => array("showitem" => "points2, joker1_2, joker2_2"),
		"8" => array("showitem" => "category3"),
		"9" => array("showitem" => "points3, joker1_3, joker2_3"),
		"10" => array("showitem" => "category4"),
		"11" => array("showitem" => "points4, joker1_4, joker2_4"),
		"12" => array("showitem" => "category5"),
		"13" => array("showitem" => "points5, joker1_5, joker2_5"),
		"14" => array("showitem" => "category6"),
		"15" => array("showitem" => "points6, joker1_6, joker2_6"),
		"16" => array("showitem" => "category_next"),
		"17" => array("showitem" => "joker3"),
		'18' => array("showitem" => "alt_text")
	),
			'columns' => array (
					't3ver_label' => array (
							'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
							'config' => array (
									'type' => 'input',
									'size' => '30',
									'max'  => '30',
							)
					),
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
									'foreign_table'       => 'tx_myquizpoll_question',
									'foreign_table_where' => 'AND tx_myquizpoll_question.pid=###CURRENT_PID### AND tx_myquizpoll_question.sys_language_uid IN (-1,0)',
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
					'fe_group' => array (
							'exclude' => 1,
							'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
							'config'  => array (
									'type'  => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('', 0),
											array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
											array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
											array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
									),
									'foreign_table' => 'fe_groups'
							)
					),
					'title' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.title',
							'config' => array (
									'type' => 'input',
									'size' => '30',
									'eval' => 'required,trim',
							)
					),
					'title_hide' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.title_hide',
							'config' => array (
									'type' => 'check',
							)
					),
					'name' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.name',
							'config' => array (
									'type' => 'text',
									'cols' => '30',
									'rows' => '5',
							),
							'defaultExtras' => 'richtext[]'
					),
					'qtype' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.0', '0'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.1', '1'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.2', '2'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.3', '3'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.4', '4'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.5', '5'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.6', '6'),
											array('LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.7', '7'),
									),
									'size' => 1,
									'maxitems' => 1,
									'default' => 1,
							)
					),
					'category' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer1' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer1',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct1' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct1',
							'config' => array (
									'type' => 'check',
							)
					),
					'points1' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points1',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_1' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_1',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_1' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_1',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category1' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category1',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer2' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer2',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct2' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct2',
							'config' => array (
									'type' => 'check',
							)
					),
					'points2' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points2',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_2' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_2',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_2' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_2',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category2' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category2',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer3' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer3',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct3' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct3',
							'config' => array (
									'type' => 'check',
							)
					),
					'points3' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points3',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_3' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_3',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_3' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_3',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category3' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category3',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer4' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer4',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct4' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct4',
							'config' => array (
									'type' => 'check',
							)
					),
					'points4' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points4',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_4' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_4',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_4' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_4',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category4' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category4',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer5' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer5',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct5' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct5',
							'config' => array (
									'type' => 'check',
							)
					),
					'points5' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points5',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_5' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_5',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_5' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_5',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category5' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category5',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'answer6' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer6',
							'config' => array (
									'type' => 'input',
									'size' => '40'
							)
					),
					'correct6' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct6',
							'config' => array (
									'type' => 'check',
							)
					),
					'points6' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points6',
							'config' => array (
									'type'     => 'input',
									'size'     => '5',
									'max'      => '7',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'joker1_6' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_6',
							'config' => array (
									'type' => 'check',
							)
					),
					'joker2_6' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_6',
							'config' => array (
									'type'     => 'input',
									'size'     => '4',
									'max'      => '4',
									'eval'     => 'int',
									'default' => 0
							)
					),
					'category6' => array (
							'exclude' => 0,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category6',
							'config' => array (
									'type' => 'select',
									'renderType' => 'selectSingle',
									'items' => array (
											array('',0),
									),
									'foreign_table' => 'tx_myquizpoll_category',
									'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'explanation' => array (
							'exclude' => 1,
							'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.explanation',
							'config' => array (
									'type' => 'text',
									'cols' => '30',
									'rows' => '5',
							),
					'defaultExtras' => 'richtext[]'
			),
			'joker3' => array (
					'exclude' => 1,
					'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker3',
					'config' => array (
							'type' => 'input',
							'size' => '30',
							'max' => '255',
							'eval' => 'trim',
					)
			),
			'points' => array (
					'exclude' => 1,
					'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points',
					'config' => array (
							'type'     => 'input',
							'size'     => '5',
							'max'      => '7',
							'eval'     => 'int',
							'default' => 1
					)
			),
			'category_next' => array (
					'exclude' => 0,
					'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category_next',
					'config' => array (
							'type' => 'select',
									'renderType' => 'selectSingle',
							'items' => array (
									array('',0),
							),
							'foreign_table' => 'tx_myquizpoll_category',
							'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
							'size' => 1,
							'minitems' => 0,
							'maxitems' => 1,
					)
			),
			'image' => array (
					'exclude' => 1,
					'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.image',
					'config' => array (
							'type' => 'group',
							'internal_type' => 'file',
							'allowed' => 'gif,png,jpeg,jpg',
							'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
							'uploadfolder' => 'uploads/tx_myquizpoll',
							'show_thumbs' => 1,
							'size' => 1,
							'minitems' => 0,
							'maxitems' => 1,
					)
			),
			'alt_text' => array (
					'exclude' => 0,
					'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.alt_text',
					'config' => array (
							'type' => 'input',
							'size' => '30',
							'max' => '255',
							'eval' => 'trim',
					)
			),
		),
);