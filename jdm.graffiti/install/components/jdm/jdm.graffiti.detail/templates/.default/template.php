<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<a href="<?=$arResult['URL_LIST']?>">Вернуться к списку</a>

<?$item = $arResult['ITEM']?>
<?$updateAt = $item['updated']?'?'.$item['updated']->format('YmdHis'):'';?>
<div>
    <img src="<?=$item['path'].$updateAt?>" />
    </br><span>Создан: <?=$item['created']->format('H:i:s Y-m-d');?></span>
    <?if(isset($item['updated'])):?>
        <span>Изменен: <?=$item['updated']->format('H:i:s Y-m-d');?></span>
    <?endif;?>
    </br>
</div>

<a href="<?=$item['url_edit']?>">Редактировать графити</a>
