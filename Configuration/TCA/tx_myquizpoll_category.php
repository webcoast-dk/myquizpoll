<?php
return array(
	'ctrl' => array (
		'title'     => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',
		'iconfile'          => 'EXT:myquizpoll/icon_tx_myquizpoll_category.gif',
	),
	'interface' => array (
		'showRecordFieldList' => 'name,pagetime,notes,celement'
	),
	'columns' => array (
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'pagetime' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.pagetime',
			'config' => array (
				'type'     => 'input',
				'size'     => '5',
				'max'      => '8',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '10000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'notes' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.notes',
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'celement' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.celement',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tt_content',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'name;;;richtext[]:rte_transform[mode=ts];1-1-1, pagetime, notes, celement')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
