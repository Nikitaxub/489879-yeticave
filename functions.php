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

function getLotsList($connection, $limit) {
    $sql = concatQueryLimit("select 
	 l.name, 
	 l.image,
	 ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
	 c.name 'category'
from lots l
join categories c on l.category_id = c.id
where ifnull(l.close_date, now() + 1) > now()
group by l.name, l.initial_price, l.image, c.name
order by l.create_date desc", $limit);

    return checkResult($connection, $sql);
}

function concatQueryLimit($sql, $limit) {
    if (is_int($limit) === true) {
        return $sql.' limit '.$limit;
    }
    else {
        /*на случай, если пользователь подсунет не численное значение,
        ограничиваю вывод 9 лотами, чтоб не грузить сервер*/
        return $sql.' limit 9';
    }

}

function getItemList($connection) {
    $sql = 'select c.name from categories c order by c.id';

    return checkResult($connection, $sql);
}

function checkResult($connection, $sql) {
    $mysqli_result = mysqli_query($connection, $sql);
    if ($mysqli_result === false) {
        $error = mysqli_error($connection);
        print('Ошибка MySQL: '. $error);
        die();
    }
    $rows = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC );
    return $rows;
}

?>