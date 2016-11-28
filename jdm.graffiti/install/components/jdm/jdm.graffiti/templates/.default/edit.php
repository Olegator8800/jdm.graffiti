<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); ?>

<?
$APPLICATION->IncludeComponent(
    'jdm:jdm.graffiti.edit',
    '',
    [
        'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
        'NEW' => 'N',
        'GRAFFITY_MANAGER' => $arResult['GRAFFITY_MANAGER'],
        'URL_GENERATOR' => $arResult['URL_GENERATOR'],
    ]
)?>
