<?php

require('functions.php');
require('data.php');

http_response_code(403);

if (!isAuthorized()) {
    $_SESSION['login'] = [];
}

$headerContent = renderTemplate('templates/header-common.php', ['login' => $_SESSION['login']]);
$mainContent = '<h1>Доступ запрещен!</h1>';
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Акцион YetiCave', 'mainClass' => ' class="container"']);

echo $layoutContent;

?>