<?php
require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$mainContent = renderTemplate('templates/index.php', ['lotsList' => $lotsList]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['header_content' => $headerContent,'main_content' => $mainContent,
    'footer_content' => $footerContent, 'title' => 'Акцион YetiCave', 'main_class' => ' class="container"']);

echo $layoutContent;

?>

