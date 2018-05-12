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

function getEntities($connection, $sql) {
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