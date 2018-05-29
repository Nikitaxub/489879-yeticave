<?php

require('mysql_helper.php');

/** 
 * форматирование числа в формате "...М ТТТ ЕЕЕ" *
 *
 * @param real $value число
 * @return string цена после форматирования
 */
function formatCost ($value) {
    return number_format(ceil($value), 0, '', ' ');
}

/** 
 * расширение отформатированного числа символом рубля
 *
 * @param string $value отформатированная цена
 * @return string отформатированная цена с символом рубля
 */
function formatCostRub ($value) {
    return formatCost ($value).'  ₽';
}

/** 
 * генерация разметки
 *
 * @param string $filename путь к шаблону
 * @param array $parameters массив параметров для заполнения шаблона
 * @return string готовый блок разметки
 */
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

/** 
 * вывод оставшегося времени в формате чч:мм
 *
 * @param string $time дата, оставшееся время до которой нужно отобразить
 * @return string оставшееся время
 */
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

/** 
 * вывод в "человеческом" формате - прошедшего времени от даты или сама дата
 *
 * @param string $time дата для анализа
 * @return string результат анализа
 */
function getPassingTime($time) {
    date_default_timezone_set('Europe/Moscow');
    $passingTimeInSec = time() - strtotime($time);

    if (intdiv($passingTimeInSec, 86400) >= 1) {
        return date('d.m.y', strtotime($time)) . ' в ' . date('H:i', strtotime($time));
    }
    return intdiv($passingTimeInSec, 3600) . ' ' . getNumEnding(intdiv($passingTimeInSec, 3600), array('час', 'часа', 'часов')) . ' ' .
        intdiv($passingTimeInSec % 3600, 60) . ' ' .getNumEnding(intdiv($passingTimeInSec % 3600, 60), array('минуту', 'минуты', 'минут')) . ' назад';
}

/** 
 * подбор нужного окончания для количества объектов в зависимости от склонения
 *
 * @param int $n количество объектов
 * @param array $forms формы склонения
 * @return mixed вывод нужной формы
 */
function getNumEnding($n, $forms) {
    return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
}

/**
 * создание ресурса подключения
 *
 * @param string $host инстанс базы данных
 * @param string $user пользователь инстанса базы данных
 * @param string $password пароль пользователя инстанса базы данных
 * @param string $db название базы данных
 * @return mysqli ресурс подключения
 */
function connectDB($host, $user, $password, $db) {
    $connection = mysqli_connect($host, $user, $password, $db);

    if ($connection === false) {
        print('Ошибка подключения: '. mysqli_connect_error());
        die();
    }

    mysqli_set_charset($connection, 'utf8');
    return $connection;
}

/**
 * получение списка лотов
 *
 * @param array $connection ресурс подключения
 * @param int $limit ограничение количества записей в выборке
 * @return array список лотов
 */
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
    group by l.name, l.initial_price, l.image, c.name, l.id
    order by l.create_date desc", $limit);

    return checkResult($connection, $sql);
}

/**
 * конкатенация с limit
 *
 * @param string $sql текст запроса
 * @param int $limit ограничение количества записей в выборке
 * @return string итоговый запрос
 */
function concatQueryLimit($sql, $limit) {
    return $sql.' limit '.$limit;
}

/**
 * получение списка категорий
 *
 * @param array $connection ресурс подключения
 * @return array список категорий
 */
function getItemList($connection) {
    $sql = 'select c.name, c.id from categories c order by c.id';

    return checkResult($connection, $sql);
}

/**
 * проверка запроса и возврат результатов выборки
 *
 * @param array $connection ресурс подключения
 * @param string $sql текст запроса
 * @return array|null
 */
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

/**
 * получение данных лота
 *
 * @param array $connection ресурс подключения
 * @param int $lotId номер лота
 * @return array|null
 */
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

/**
 * получение ставок лота
 *
 * @param array $connection ресурс подключения
 * @param int $lotId номер лота
 * @return array|null
 */
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

/**
 * редирект на страницу с 404 ошибкой
 */
function redirect404() {
    header("Location: 404.php");
}

/**
 * редирект на страницу с 403 ошибкой
 */
function redirect403() {
    header("Location: 403.php");
}

/**
 * проверка использования метода post и наличия массива параметров
 *
 * @param array $postArray массив параметров
 * @return bool
 */
function isPost($postArray) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST[$postArray])) {
        return true;
    }
    return false;
}

/**
 * выполнение подготовленного выражения
 *
 * @param array $connection Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 */
function dbExecuteStmt ($connection, $sql, $data) {
    $stmt = db_get_prepare_stmt($connection, $sql, $data);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

/**
 * добавление нового пользователя в таблицу базы данных
 *
 * @param array $connection Ресурс соединения
 * @param array $data параметры для подготовленного выражения
 */
function dbInsertUser($connection, $data) {
    $sql = 'insert into users (name, email, password_hash, avatar, contacts) values (?,?,?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

/**
 * добавление нового лота в таблицу базы данных
 *
 * @param array $connection Ресурс соединения
 * @param array $data параметры для подготовленного выражения
 */
function dbInsertLot($connection, $data) {
    $sql = 'insert into lots (name, category_id, description, initial_price, bet_increment, close_date, image, author_id) values (?,?,?,?,?,?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

/**
 * проверска существования пользователя с указанным емайл
 *
 * @param array $connection Ресурс соединения
 * @param $email емайл пользователя
 * @return array|null
 */
function isUsedEmail($connection, $email) {
    return checkResult($connection, "select * from users where email = '$email'");
}

/**
 * сопоставление хэшей переданного пароля и соответствующего емайл, хрянящемуся в базе данных
 *
 * @param array $connection Ресурс соединения
 * @param $email емайл пользователя
 * @param $password пароль пользователя
 * @return bool
 */
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

/**
 * получение данных пользователя
 *
 * @param array $connection Ресурс соединения
 * @param $email емайл пользователя
 * @return array|null
 */
function getUser($connection, $email) {
    $sql = "select email, name, avatar, contacts, id from users where email = '{$email}' limit 1";

    return checkResult($connection, $sql);
}

/**
 * проверка, авторизован ли пользователь
 *
 * @return bool
 */
function isAuthorized() {
    $session = getSession();
    if (isset($session['email'])) {
        return true;
    }
    return false;
}

/**
 * добавление новой ставки на лот в таблицу базы данных
 * @param array $connection Ресурс соединения
 * @param array $data параметры для подготовленного выражения
 */
function dbInsertBet($connection, $data) {
    $sql = 'insert into bets (price, user_id, lot_id) values (?,?,?)';

    dbExecuteStmt ($connection, $sql, $data);
}

/**
 * 'сворачивание' данных post в массив
 * 
 * @return array
 */
function getSession() {
    if (isset($_SESSION['login'])) {
        return $_SESSION['login'];
    };
    return [];
}