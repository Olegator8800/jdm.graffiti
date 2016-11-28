<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'SEF_MODE' => [
            'index' => [
                'NAME' => GetMessage('JDM_GRAFFITI_PARAMETERS_INDEX_PAGE'),
                'DEFAULT' => 'index.php',
                'VARIABLES' => [],
            ],
            'new' => [
                'NAME' => GetMessage('JDM_GRAFFITI_PARAMETERS_NEW_PAGE'),
                'DEFAULT' => 'new',
                'VARIABLES' => [],
            ],
            'show' => [
                'NAME' => GetMessage('JDM_GRAFFITI_PARAMETERS_SHOW_PAGE'),
                'DEFAULT' => '#ELEMENT_ID#',
                'VARIABLES' => ['ELEMENT_ID'],
            ],
            'edit' => [
                'NAME' => GetMessage('JDM_GRAFFITI_PARAMETERS_EDIT_PAGE'),
                'DEFAULT' => '#ELEMENT_ID#/edit',
                'VARIABLES' => ['ELEMENT_ID'],
            ],
        ],
        'PICTURE_DIR_PATH' => [
            'PARENT' => 'BASE',
            'NAME' => GetMessage('JDM_GRAFFITI_PARAMETERS_PICTURE_DIR_PATH'),
            'TYPE' => 'TEXT',
            'DEFAULT' => '/graffiti/',
        ],
        'CACHE_TIME' => [],
    ],
];
