<?php
require('functions.php');
require('data.php');

$indexContent = renderTemplate('templates/index.php', ['lotsList' => $lots, 'remainingTime' => getRemainingTime()]);
$layoutContent = renderTemplate('templates/layout.php', ['itemList' => $categories, 'content' => $indexContent,
    'title' => 'Акцион YetiCave', 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'is_auth' => $is_auth]);

echo $layoutContent;

?>

