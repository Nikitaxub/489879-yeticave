<?php

require('mysql_helper.php');

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

    if (intdiv($passingTimeInSec, 86400) >= 1) {
        return date('d.m.y', strtotime($time)) . ' в ' . date('H:i', strtotime($time));
    }
    return getNumEnding(intdiv($passingTimeInSec, 3600), array('час', 'часа', 'часов')) . ' ' .
        getNumEnding(intdiv($passingTimeInSec % 3600, 60), array('минуту', 'минуты', 'минут')) . ' назад';
}

function getNumEnding($number, $endingArray)
{
    $returnValue = '';
    $number = $number % 100;
    if ($number>=10 && $number<=19) {
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
    if ($returnValue == '' && $endingArray == ['минуту', 'минуты', 'минут']) {
        $returnValue = '0 минут';
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
    $sql = 'select c.name, c.id from categories c order by c.id';

    return checkResult($connection, $sql);
}

function checkResult($connection, $sql) {
    $mysqliResult = mysqli_query($connection, $sql);
    $error = '';
    if ($mysqliResult === false) {
        $error = mysqli_error($connection);
        print('Ошибка MySQL: '. $error);
        die();
    }
    $rows = mysqli_fetch_all($mysqliResult, MYSQLI_ASSOC );
    return $rows;
}

function getLot($connection, $lotId) {
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
    l.close_date,
    (select a.email from users a where a.id = l.author_id) 'author',
    ifnull((
      select ulb.email 
      from bets lb 
      join users ulb on lb.user_id = ulb.id 
      where l.id = lb.lot_id 
      order by price desc, create_date desc 
      limit 1), '') 'lastBet'
    from lots l
    join categories c on l.category_id = c.id
    where l.id = $lotId";

    return checkResult($connection, $sql);
}

function getBetList($connection, $lotId) {
    $sql = "select
    b.create_date,
    b.price,
    u.name 'user_name'
    from bets b
    join users u on b.user_id = u.id
    where b.lot_id = $lotId
    order by b.price desc";

    return checkResult($connection, $sql);
}

function redirect404() {
    header("Location: 404.php");
}

function redirect403() {
    header("Location: 403.php");
}

function isPost($postArray) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST[$postArray])) {
        return true;
    }
    return false;
}

function dbExecuteStmt ($connection, $sql, $data) {
    $stmt = db_get_prepare_stmt($connection, $sql, $data);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

function dbInsertUser($connection, $data) {
    $sql = 'insert into users (name, email, password_hash, avatar, contacts) values (?,?,?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

function dbInsertLot($connection, $data) {
    $sql = 'insert into lots (name, category_id, description, initial_price, bet_increment, close_date, image, author_id) values (?,?,?,?,?,?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

function isUsedEmail($connection, $email) {
    return checkResult($connection, "select * from users where email = '$email'");
}

function checkAuth($connection, $email, $password) {
    $passworHash = checkResult($connection, "select password_hash from users where email = '{$email}' limit 1");
    $newHash = '';
    if (isset($passworHash[0]) && password_verify($password, $passworHash[0]['password_hash'])) {
        if (password_needs_rehash($passworHash[0]['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            dbExecuteStmt ($connection, 'update users set password_hash = ? where email = ?', [$newHash, $email]);
        }
        return true;
    }
    return false;
}

function getUser($connection, $email) {
    $sql = "select email, name, avatar, contacts, id from users where email = '{$email}' limit 1";

    return checkResult($connection, $sql);
}

function isAuthorized() {
    $session = getSession();
    if (isset($session['email'])) {
        return true;
    }
    return false;
}

function dbInsertBet($connection, $data) {
    $sql = 'insert into bets (price, user_id, lot_id) values (?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

function getSession() {
    if (isset($_SESSION['login'])) {
        return $_SESSION['login'];
    };
    return [];
}