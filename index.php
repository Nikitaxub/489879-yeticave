<?php
require('data.php');

require('functions.php');

$con = connectDB($db_host, $db_user, $db_password, $db_name);

$categories = getCategories($con);

$lots = getLots($con);

$indexContent = renderTemplate('templates/index.php', ['lotsList' => $lots, 'remainingTime' => getRemainingTime()]);
$layoutContent = renderTemplate('templates/layout.php', ['itemList' => $categories, 'content' => $indexContent,
    'title' => 'Акцион YetiCave', 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'is_auth' => $is_auth]);

echo $layoutContent;

?>

