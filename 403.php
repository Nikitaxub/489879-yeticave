<?php

require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', []);
$mainContent = '<h1>Доступ запрещен!</h1>';
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Акцион YetiCave', 'mainClass' => ' class="container"']);

echo $layoutContent;

?>