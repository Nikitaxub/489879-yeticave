<?php
function formatCost ($value) {
    return number_format(ceil($value), 0, '', ' ').'  ₽';
}

function renderTemplate($filename, $parameters = []) {
    if (is_file($filename) && is_readable($filename)) {
        ob_start();
        extract($parameters);
        require($filename);
        return ob_get_clean();
    } else {
        return '';
    }
}

function getRemainingTime() {
    date_default_timezone_set('Europe/Moscow');
    $remainingTimeInSec = strtotime( 'tomorrow') - time();
    return intdiv($remainingTimeInSec, 3600).':'.intdiv($remainingTimeInSec % 3600,  60);
}

function connectDB($host, $user, $password, $db) {
    $connection = mysqli_connect($host, $user, $password, $db);

    if ($connection === false) {
        print('Ошибка подключения: '. mysqli_connect_error());
        die();
    }

    mysqli_set_charset($connection, 'utf8');
    return $connection;
}

function getList($connection, $list, $parameters = []) {
    switch ($list) {
        case 'categoriesList':
            $sql = 'select c.name from categories c order by c.id';
            break;
        case 'lotsList':
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
order by l.create_date desc
limit {$parameters['lotsLimit']}";
            break;
    }

    $res = mysqli_query($connection, $sql);
    if ($res === false) {
        $error = mysqli_error($connection);
        print('Ошибка MySQL: '. $error);
        die();
    }

    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC );

    return $rows;
}
?>