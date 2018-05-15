<?php

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$db_name = 'yeti_cave';
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';

$connection = connectDB($db_host, $db_user, $db_password, $db_name);

$lotsLimit = 9;

$itemList = getItemList($connection);

$lotsList = getLotsList($connection, $lotsLimit);