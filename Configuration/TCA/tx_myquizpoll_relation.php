<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:myquizpoll/icon_tx_myquizpoll_relation.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,user_id,question_id,textinput,checked1,checked2,checked3,checked4,checked5,checked6,points,nextcat'
    ],
    'columns' => [
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
                'foreign_table' => 'tx_myquizpoll_relation',
                'foreign_table_where' => 'AND tx_myquizpoll_relation.pid=###CURRENT_PID### AND tx_myquizpoll_relation.sys_language_uid IN (-1,0)',
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
        'user_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.user_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_myquizpoll_result',
                'foreign_table_where' => 'AND tx_myquizpoll_result.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_result.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'question_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.question_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_myquizpoll_question',
                'foreign_table_where' => 'AND tx_myquizpoll_question.pid=###CURRENT_PID### ORDER BY tx_myquizpoll_question.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'textinput' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.textinput',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '3',
            ]
        ],
        'checked1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked1',
            'config' => [
                'type' => 'check',
            ]
        ],
        'checked2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked2',
            'config' => [
                'type' => 'check',
            ]
        ],
        'checked3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked3',
            'config' => [
                'type' => 'check',
            ]
        ],
        'checked4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked4',
            'config' => [
                'type' => 'check',
            ]
        ],
        'checked5' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked5',
            'config' => [
                'type' => 'check',
            ]
        ],
        'checked6' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.checked6',
            'config' => [
                'type' => 'check',
            ]
        ],
        'points' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_relation.points',
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '8',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'nextcat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.nextcat',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_myquizpoll_category',
                'foreign_table_where' => 'ORDER BY tx_myquizpoll_category.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, user_id, question_id, textinput, checked1, checked2, checked3, checked4, checked5, checked6, points, nextcat']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ]
];
