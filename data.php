<?php

$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$db_name = 'yeti_cave';
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';

$con = connectDB($db_host, $db_user, $db_password, $db_name);

$lots_limit = 9;
$sql_lots = "
select 
	 l.name, 
	 l.image,
	 ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
	 c.name 'category'
from lots l
join categories c on l.category_id = c.id
where ifnull(l.close_date, now() + 1) > now()
group by l.name, l.initial_price, l.image, c.name
order by l.create_date desc
limit $lots_limit";

$sql_categories = 'select c.name from categories c order by c.id';

$categories = getEntities($con, $sql_categories);

$lots = getEntities($con, $sql_lots);