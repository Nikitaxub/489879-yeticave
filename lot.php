<?php
require('functions.php');
require('data.php');

if (isset($_GET['lot_id'])) {
    $lot_id = intval($_GET['lot_id']);
}
else {
    header("Location: ./404.php");
}

$lot = getLot($connection, $lot_id);

if (!$lot[0]) {
    header("Location: ./404.php");
}

$betsList = getBetList($connection, $lot_id);

$headerContent = renderTemplate('templates/header_common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$mainContent = renderTemplate('templates/lot.php', ['lot' => $lot[0], 'betsList' => $betsList]);
$footerContent = renderTemplate('templates/footer_common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['header_content' => $headerContent, 'main_content' => $mainContent,
    'footer_content' => $footerContent, 'title' => $lot[0]['name'], 'main_class' => '']);

echo $layoutContent;

?>