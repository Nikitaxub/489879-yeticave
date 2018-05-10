<?php
require('data.php');

require('functions.php');

$con = mysqli_connect("localhost", "root", "", "yeti_cave");
mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    return;
}

$sql = "select c.name from categories c order by c.id";

$res = mysqli_query($con, $sql);
if (!$res) {
    $error = mysqli_error($con);
    print("Ошибка MySQL: ". $error);
    return;
}

$categories = mysqli_fetch_all($res, MYSQLI_ASSOC );

if (mysqli_num_rows($res) == 0) {
    print("Ошибка MySQL: cadonoex");
    return;
}

$sql = "
select 
	 l.name, 
	 l.image,
	 ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
	 c.name 'category'
from lots l
join categories c on l.category_id = c.id
where ifnull(l.close_date, now() + 1) > now()
group by l.name, l.initial_price, l.image, c.name
order by l.create_date desc";

$res = mysqli_query($con, $sql);
if (!$res) {
    $error = mysqli_error($con);
    print("Ошибка MySQL: ". $error);
    return;
}

$lots = mysqli_fetch_all($res, MYSQLI_ASSOC );

if (mysqli_num_rows($res) == 0) {
    print("Ошибка MySQL: lodonoex");
    return;
}

$indexContent = renderTemplate('templates/index.php', ['lotsList' => $lots, 'remainingTime' => getRemainingTime()]);
$layoutContent = renderTemplate('templates/layout.php', ['itemList' => $categories, 'content' => $indexContent,
    'title' => 'Акцион YetiCave', 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'is_auth' => $is_auth]);

echo $layoutContent;

?>

