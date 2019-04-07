<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'fe_group' => 'fe_group',
        ],
        'iconfile' => 'EXT:myquizpoll/icon_tx_myquizpoll_question.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,fe_group,title,title_hide,name,qtype,category,answer1,correct1,points1,joker1_1,joker2_1,category1,answer2,correct2,points2,joker1_2,joker2_2,category2,answer3,correct3,points3,joker1_3,joker2_3,category3,answer4,correct4,points4,joker1_4,joker2_4,category4,answer5,correct5,points5,joker1_5,joker2_5,category5,answer6,correct6,points6,joker1_6,joker2_6,category6,explanation,joker3,points,category_next,image,alt_text'
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;2;;2-2-2, name;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];3-3-3, image;;18, qtype;;3, answer1;;4, correct1;;5, answer2;;6, correct2;;7, answer3;;8, correct3;;9, answer4;;10, correct4;;11, answer5;;12, correct5;;13, answer6;;14, correct6;;15, points;;16, explanation;;17;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]']
    ],
    'palettes' => [
        '1' => ['showitem' => 'fe_group'],
        '2' => ['showitem' => 'title_hide'],
        '3' => ['showitem' => 'category'],
        "4" => ["showitem" => "category1"],
        "5" => ["showitem" => "points1, joker1_1, joker2_1"],
        "6" => ["showitem" => "category2"],
        "7" => ["showitem" => "points2, joker1_2, joker2_2"],
        "8" => ["showitem" => "category3"],
        "9" => ["showitem" => "points3, joker1_3, joker2_3"],
        "10" => ["showitem" => "category4"],
        "11" => ["showitem" => "points4, joker1_4, joker2_4"],
        "12" => ["showitem" => "category5"],
        "13" => ["showitem" => "points5, joker1_5, joker2_5"],
        "14" => ["showitem" => "category6"],
        "15" => ["showitem" => "points6, joker1_6, joker2_6"],
        "16" => ["showitem" => "category_next"],
        "17" => ["showitem" => "joker3"],
        '18' => ["showitem" => "alt_text"]
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_question',
                'foreign_table_where' => 'AND tx_myquizpoll_question.pid=###CURRENT_PID### AND tx_myquizpoll_question.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        -1
                    ],
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        -2
                    ],
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        '--div--'
                    ]
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title ASC',
                'enableMultiSelectFilterTextfield' => true
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ]
        ],
        'title_hide' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.title_hide',
            'config' => [
                'type' => 'check',
            ]
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.name',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
            'defaultExtras' => 'richtext[]'
        ],
        'qtype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.0', '0'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.1', '1'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.2', '2'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.3', '3'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.4', '4'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.5', '5'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.6', '6'],
                    ['LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.qtype.I.7', '7'],
                ],
                'size' => 1,
                'maxitems' => 1,
                'default' => 1,
            ]
        ],
        'category' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer1',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct1',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points1' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points1',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_1',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_1',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category1' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category1',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer2',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct2',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points2' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points2',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_2',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_2',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category2' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category2',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer3',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct3',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points3' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points3',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_3',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_3',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category3' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category3',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer4',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct4',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points4' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points4',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_4',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_4',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category4' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category4',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer5' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer5',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct5' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct5',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points5' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points5',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_5' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_5',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_5' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_5',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category5' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category5',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'answer6' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.answer6',
            'config' => [
                'type' => 'input',
                'size' => '40'
            ]
        ],
        'correct6' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.correct6',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points6' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points6',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'joker1_6' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker1_6',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2_6' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker2_6',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'category6' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category6',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'explanation' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.explanation',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
            'defaultExtras' => 'richtext[]'
        ],
        'joker3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.joker3',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
                'eval' => 'trim',
            ]
        ],
        'points' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.points',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '7',
                'eval' => 'int',
                'default' => 1
            ]
        ],
        'category_next' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.category_next',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'AND tx_myquizpoll_category.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'image' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.image',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/tx_myquizpoll',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'alt_text' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_question.alt_text',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
                'eval' => 'trim',
            ]
        ],
    ],
];
