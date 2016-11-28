<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();


$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if(!$isAjaxRequest) {
    die('Must be an ajax request');
}

$GLOBALS['APPLICATION']->RestartBuffer();

$gm = $arResult['GRAFFITY_MANAGER'];

$error = '';
$token = '';

$id = (int) $_POST['id'];
$password = $_POST['password'];

try {
    $token = $gm->getAccessToken($id, $password);

} catch (Exception $e) {
    $error = $e->getMessage();
}

echo json_encode(['token' => $token, 'error' => $error]);
die;
