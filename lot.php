<?php

require('functions.php');
require('data.php');

if (ctype_digit($_GET['lot_id'])) {
    $lot_id = intval($_GET['lot_id']);
} else {
    redirect404();
}

$lot = getLot($connection, $lot_id);

if (!$lot[0]) {
    redirect404();
}

$betsList = getBetList($connection, $lot_id);

$headerContent = renderTemplate('templates/header-common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$navContent = renderTemplate('templates/nav-items.php', []);
$mainContent = renderTemplate('templates/lot.php', ['lot' => $lot[0], 'betsList' => $betsList, 'navContent' => $navContent]);
$footerContent = renderTemplate('templates/footerCommon.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => $lot[0]['name'], 'mainClass' => '']);

echo $layoutContent;

?>