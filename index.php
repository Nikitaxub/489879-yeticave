<?php
$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$lotsList = [
	0 => [
		'title' => '2014 Rossignol District Snowboard',
		'category' => 'Доски и лыжи',
		'cost' => '10999',
		'image' => 'img/lot-1.jpg'
	],
	1 => [
		'title' => 'DC Ply Mens 2016/2017 Snowboard',
		'category' => 'Доски и лыжи',
		'cost' => '159999',
		'image' => 'img/lot-2.jpg'
	],
	2 => [
		'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
		'category' => 'Крепления',
		'cost' => '8000',
		'image' => 'img/lot-3.jpg'
	],
	3 => [
		'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
		'category' => 'Ботинки',
		'cost' => '10999',
		'image' => 'img/lot-4.jpg'
	],
	4 => [
		'title' => 'Куртка для сноуборда DC Mutiny Charocal',
		'category' => 'Одежда',
		'cost' => '7500',
		'image' => 'img/lot-5.jpg'
	],
	5 => [
		'title' => 'Маска Oakley Canopy',
		'category' => 'Разное',
		'cost' => '5400',
		'image' => 'img/lot-6.jpg'
	]
];

$itemList = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

require('functions.php');

$indexContent = renderTemplate('templates/index.php', ['lotsList' => $lotsList]);
$layoutContent = renderTemplate('templates/layout.php', ['itemList' => $itemList, 'content' => $indexContent,
    'title' => 'Акцион YetiCave', 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'is_auth' => $is_auth]);

echo $layoutContent;

?>

