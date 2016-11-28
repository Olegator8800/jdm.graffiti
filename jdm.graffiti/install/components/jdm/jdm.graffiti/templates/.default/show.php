<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); ?>

<?
$APPLICATION->IncludeComponent(
    'jdm:jdm.graffiti.detail',
    '',
    [
        'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
        'GRAFFITY_MANAGER' => $arResult['GRAFFITY_MANAGER'],
        'URL_GENERATOR' => $arResult['URL_GENERATOR'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
    ]
)?>
