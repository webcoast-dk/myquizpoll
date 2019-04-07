<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting',
        'label' => 'answer_no',
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
        'iconfile' => 'EXT:myquizpoll/icon_tx_myquizpoll_voting.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,ip,answer_no,question_id,foreign_val'
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
                'foreign_table' => 'tx_myquizpoll_voting',
                'foreign_table_where' => 'AND tx_myquizpoll_voting.pid=###CURRENT_PID### AND tx_myquizpoll_voting.sys_language_uid IN (-1,0)',
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
        'ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.ip',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ]
        ],
        'question_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.question_id',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_myquizpoll_question',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'foreign_val' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.foreign_val',
            'config' => [
                'type' => 'input',
                'size' => '15',
                'max' => '255'
            ]
        ],
        'answer_no' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_voting.answer_no',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '100',
                    'lower' => '0'
                ],
                'default' => 0
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, ip,question_id,foreign_val,answer_no']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ]
];
