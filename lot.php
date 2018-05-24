<?php

require('functions.php');
require('data.php');

if (ctype_digit($_GET['lot_id'])) {
    $lotId = intval($_GET['lot_id']);
} else {
    redirect404();
}

if (!isAuthorized()) {
    $_SESSION['login'] = [];
}

$lot = getLot($connection, $lotId);

if (!$lot[0]) {
    redirect404();
}

$betsList = getBetList($connection, $lotId);

$headerContent = renderTemplate('templates/header-common.php', ['login' => $_SESSION['login']]);
$navContent = renderTemplate('templates/nav-items.php', []);
$mainContent = renderTemplate('templates/lot.php', ['lot' => $lot[0], 'betsList' => $betsList, 'navContent' => $navContent]);
$footerContent = renderTemplate('templates/footerCommon.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => $lot[0]['name'], 'mainClass' => '']);

echo $layoutContent;

?>