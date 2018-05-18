<?php
function formatCost ($value) {
    return number_format(ceil($value), 0, '', ' ');
}

function formatCostRub ($value) {
    return formatCost ($value).'  ₽';
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

function getRemainingTime($time) {
    date_default_timezone_set('Europe/Moscow');
    if ($time == '') {
        return '--:--';
    }
    $remainingTimeInSec = strtotime($time) - time();
    if ($remainingTimeInSec > 0) {
        return intdiv($remainingTimeInSec, 3600) . ':' . intdiv($remainingTimeInSec % 3600, 60);
    }
    return '00:00';
}

function getPassingTime($time) {
    date_default_timezone_set('Europe/Moscow');
    $passingTimeInSec = time() - strtotime($time);

    if (intdiv($passingTimeInSec, 86400) > 1) {
        return date('d.m.y', strtotime($time)) . ' в ' . date('H:i', strtotime($time));
    }
    return $returnValue = getNumEnding(intdiv($passingTimeInSec, 3600), array('час', 'часа', 'часов')) . ' ' .
        getNumEnding(intdiv($passingTimeInSec % 3600, 60), array('минута', 'минуты', 'минут')) . ' назад';

}

function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $returnValue = $number.' '.$endingArray[2];
    } else {
        $i = $number % 10;
        switch ($i)
        {
            case (0): $returnValue = ''; break;
            case (1): $returnValue = $number.' '.$endingArray[0]; break;
            case (2):
            case (3):
            case (4): $returnValue = $number.' '.$endingArray[1]; break;
            default: $returnValue = $number.' '.$endingArray[2];
        }
    }
    return $returnValue;
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
    l.id 'lot_id',
    l.name, 
    l.image,
    ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
    c.name 'category',
    l.close_date
    from lots l
    join categories c on l.category_id = c.id
    where ifnull(l.close_date, now() + 1) > now()
    group by l.name, l.initial_price, l.image, c.name
    order by l.create_date desc", $limit);

    return checkResult($connection, $sql);
}

function concatQueryLimit($sql, $limit) {
    return $sql.' limit '.$limit;
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

function getLot($connection, $lot_id) {
    $sql = "select 
    l.name, 
    l.image,
    l.description,
    ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
    case
      when (select count(*) from bets bc where l.id = bc.lot_id) > 0
        then (select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1) + l.bet_increment
      else l.initial_price
    end 'min_bet',
    c.name 'category',
    l.close_date
    from lots l
    join categories c on l.category_id = c.id
    where l.id = $lot_id";

    return checkResult($connection, $sql);
}

function getBetList($connection, $lot_id) {
    $sql = "select
    b.create_date,
    b.price,
    u.name 'user_name'
    from bets b
    join users u on b.user_id = u.id
    where b.lot_id = $lot_id
    order by b.price desc";

    return checkResult($connection, $sql);
}

?>