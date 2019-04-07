<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY name',
        'iconfile' => 'EXT:myquizpoll/icon_tx_myquizpoll_category.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'name,pagetime,notes,celement'
    ],
    'columns' => [
        'name' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
                'eval' => 'trim',
            ]
        ],
        'pagetime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.pagetime',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '8',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '10000',
                    'lower' => '0'
                ],
                'default' => 0
            ]
        ],
        'notes' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.notes',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ]
        ],
        'celement' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_category.celement',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tt_content',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'name;;;richtext[]:rte_transform[mode=ts];1-1-1, pagetime, notes, celement']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ]
];
