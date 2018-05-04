<?php
// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

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
