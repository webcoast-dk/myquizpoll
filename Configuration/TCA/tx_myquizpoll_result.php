<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result',
        'label' => 'name',
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
        'iconfile' => 'EXT:myquizpoll/icon_tx_myquizpoll_result.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,name,email,homepage,ip,p_or_a,p_max,percent,o_max,o_percent,qids,cids,fids,sids,joker1,joker2,joker3,firsttime,lasttime,lastcat,nextcat,fe_uid,start_uid'
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
                'foreign_table' => 'tx_myquizpoll_result',
                'foreign_table_where' => 'AND tx_myquizpoll_result.pid=###CURRENT_PID### AND tx_myquizpoll_result.sys_language_uid IN (-1,0)',
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
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'homepage' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.homepage',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.ip',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'p_or_a' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.p_or_a',
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '8',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'p_max' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.p_max',
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '8',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'percent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.percent',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'o_max' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.o_max',
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '8',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'o_percent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.o_percent',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0
            ]
        ],
        'qids' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.qids',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_myquizpoll_question',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 200,
            ]
        ],
        'cids' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.cids',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_myquizpoll_question',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 200,
            ]
        ],
        'fids' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.fids',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_myquizpoll_question',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 200,
            ]
        ],
        'sids' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.sids',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_myquizpoll_question',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 100,
            ]
        ],
        'joker1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker1',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker2',
            'config' => [
                'type' => 'check',
            ]
        ],
        'joker3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.joker3',
            'config' => [
                'type' => 'check',
            ]
        ],
        'firsttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.firsttime',
            'config' => [
                'type' => 'input',
                'size' => '12',
                'max' => '20',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0'
            ]
        ],
        'lasttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.lasttime',
            'config' => [
                'type' => 'input',
                'size' => '12',
                'max' => '20',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0'
            ]
        ],
        'lastcat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.lastcat',
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
        'fe_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.fe_uid',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'start_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:myquizpoll/locallang_db.xml:tx_myquizpoll_result.start_uid',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name;;;richtext[]:rte_transform[mode=ts], email, homepage, ip, p_or_a;;2, qids;;3, cids, fids, sids, firsttime, lasttime, lastcat, nextcat, fe_uid, start_uid']
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        "2" => ["showitem" => "p_max, percent, o_max, o_percent"],
        "3" => ["showitem" => "joker1, joker2, joker3"]
    ]
];
