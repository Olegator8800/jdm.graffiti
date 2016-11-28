<?php

if(!check_bitrix_sessid()) return;

if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => 'Ошибка удаления модуля',
        'DETAILS' => $ex->GetString(),
        'HTML' => true,
    ]);
} else {
    echo CAdminMessage::ShowNote('Модуль успешно удален');
}

?>
<form action="<?echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?echo LANG ?>">
    <input type="submit" name="" value="<?echo GetMessage("MOD_BACK"); ?>">
<form>
