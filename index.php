<?php
require('data.php');

require('functions.php');

$indexContent = renderTemplate('templates/index.php', ['lotsList' => $lotsList, 'remainingTime' => getRemainingTime()]);
$layoutContent = renderTemplate('templates/layout.php', ['itemList' => $itemList, 'content' => $indexContent,
    'title' => 'Акцион YetiCave', 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'is_auth' => $is_auth]);

echo $layoutContent;
?>

