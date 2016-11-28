<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<a href="<?=$arResult['URL_ADD']?>">Добавить графити</a>

<?if($arResult['ITEMS']):?>
    <ul>
        <?foreach($arResult['ITEMS'] as $item):?>
            <?$updateAt = $item['updated']?'?'.$item['updated']->format('YmdHis'):'';?>
            <li>
                <a href="<?=$item['url_show']?>"><img height="200" width="200" src="<?=$item['path'].$updateAt?>" /></a>
                </br><span>Создан: <?=$item['created']->format('H:i:s Y-m-d');?></span>
                <?if(isset($item['updated'])):?>
                    <span>Изменен: <?=$item['updated']->format('H:i:s Y-m-d');?></span>
                <?endif;?>
                <a class="j-jdm_graffity-edit" data-id="<?=$item['id']?>" href="<?=$item['url_edit']?>">Редактировать</a>
                </br>
            </li>
        <?endforeach;?>
    </ul>
<?endif;?>
