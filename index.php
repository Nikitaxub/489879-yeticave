<?php
require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$mainContent = renderTemplate('templates/index.php', ['lotsList' => $lotsList]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent,'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Акцион YetiCave', 'mainClass' => ' class="container"']);

echo $layoutContent;

?>

