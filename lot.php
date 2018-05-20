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
$nav_content = renderTemplate('templates/nav-items.php', []);
$mainContent = renderTemplate('templates/lot.php', ['lot' => $lot[0], 'betsList' => $betsList, 'nav_content' => $nav_content]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['header_content' => $headerContent, 'main_content' => $mainContent,
    'footer_content' => $footerContent, 'title' => $lot[0]['name'], 'main_class' => '']);

echo $layoutContent;

?>