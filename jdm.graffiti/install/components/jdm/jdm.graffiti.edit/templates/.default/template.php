<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<?
    $jdmJsConfig = [
        'isNew' => $arParams['NEW'] == 'Y'?true:false,
        'urlApiSave' => $arResult['URL_API_SAVE'],
        'urlApiCheck' => $arResult['URL_API_CHECK'],
    ];

    $GLOBALS['APPLICATION']->AddHeadString(sprintf('<script>var jdm_graffiti = %s;</script>', json_encode($jdmJsConfig)));

    CJSCore::Init(['jquery']);
    $GLOBALS['APPLICATION']->AddHeadScript($templateFolder.'/js/sketch.min.js');
    $GLOBALS['APPLICATION']->AddHeadScript($templateFolder.'/js/script.js');
?>

<a href="<?=$arResult['URL_LIST']?>">Вернуться к списку</a>

<div class="j-jdm_graffiti">
    <?if($item = $arResult['ITEM']):?>
        <?$updateAt = $item['updated']?'?'.$item['updated']->format('YmdHis'):'';?>
        <div>
            <canvas class="j-jdm_graffiti-canvas" width="<?=$arResult['GRAFFITI_WIDTH']?>" height="<?=$arResult['GRAFFITI_HEIGHT']?>" data-id="<?=$item['id']?>" data-path="<?=$item['path'].$updateAt?>" style="background: url(<?=$item['path'].$updateAt?>) no-repeat center center;"></canvas>
        </div>
    <?else:?>
        <div>
            <canvas class="j-jdm_graffiti-canvas" width="<?=$arResult['GRAFFITI_WIDTH']?>" height="<?=$arResult['GRAFFITI_HEIGHT']?>" style="background-color: white;"></canvas>
        </div>

        Пароль: <input class="j-jdm_graffiti-password" type="password" style="color: red;" />
    <?endif;?>

    <canvas class="j-jdm_graffiti-canvas_result" width="<?=$arResult['GRAFFITI_WIDTH']?>" height="<?=$arResult['GRAFFITI_HEIGHT']?>" style="display: none;"></canvas>
    <button class="j-jdm_graffiti-save">Сохранить</button>
</div>
