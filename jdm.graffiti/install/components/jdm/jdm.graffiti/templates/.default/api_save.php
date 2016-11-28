<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();


$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if(!$isAjaxRequest) {
    die('Must be an ajax request');
}

$GLOBALS['APPLICATION']->RestartBuffer();

$gm = $arResult['GRAFFITY_MANAGER'];
$urlGenerator = $arResult['URL_GENERATOR'];

$error = '';
$url = '';

if (isset($_POST['new']) && $_POST['new']) {

    $image = $_POST['image'];
    $password = $_POST['password'];

    try {
        $id = $gm->createNewGraffity($image, $password);

        $url = $urlGenerator->generate('show', ['ELEMENT_ID' => $id]);

        $GLOBALS['CACHE_MANAGER']->ClearByTag('jdm-graffiti-list');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

} else {
    $id = (int) $_POST['id'];
    $image = $_POST['image'];
    $token = $_POST['token'];

    try {
        $gm->updateGraffity($id, $image, $token);

        $GLOBALS['CACHE_MANAGER']->ClearByTag("jdm-graffiti-{$id}");
        $GLOBALS['CACHE_MANAGER']->ClearByTag("jdm-graffiti-detail-{$id}");
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

echo json_encode(['url' => $url, 'error' => $error]);
die;
