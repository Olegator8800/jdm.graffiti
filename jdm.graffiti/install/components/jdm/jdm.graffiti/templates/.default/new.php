<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); ?>

<?
$APPLICATION->IncludeComponent(
    'jdm:jdm.graffiti.edit',
    '',
    [
        'NEW' => 'Y',
        'GRAFFITY_MANAGER' => $arResult['GRAFFITY_MANAGER'],
        'URL_GENERATOR' => $arResult['URL_GENERATOR'],
    ]
)?>
