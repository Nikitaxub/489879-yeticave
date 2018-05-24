<?php

require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$imagePath = '';

array_unshift ($itemList, ['id' => 0, 'name' => 'Выберите категорию']);

$errors = [];

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['newLot'])) {
    $lot = $_POST['newLot'];

    $requiredFields = ['name', 'category_id', 'description', 'initial_price', 'bet_increment', 'close_date'];

    foreach ($requiredFields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Заполните это поле';
        }
    }

    if ($lot['category_id'] == 0) {
        $errors['category_id'] = 'Категория не выбрана';
    }

    foreach ($lot as $key => $value) {
        if ($key == "initial_price" || $key == "bet_increment") {
            if (ctype_digit($value)) {
                $lot[$key] = intval($value);
            } elseif (!$errors[$key]) {
                $errors[$key] = 'Поле должно содержать только цифры';
            }
        }
    }

    if (isset($_FILES['lot_image'])) {
        $fileSize = $_FILES['lot_image']['size'];
        if ($fileSize > 0) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileName = $_FILES['lot_image']['tmp_name'];
            $fileType = finfo_file($finfo, $fileName);
            if (!in_array($fileType, $lotImageMIMETypes)) {
                $errors['lot_image'] = 'Картинка не в формате jpeg/png';
            }
            finfo_close($finfo);
        } else {
            $errors['lot_image'] = 'Загрузите картинку в формате jpeg/png/webp';
        }
    }

    if (!empty($errors)) {
        foreach ($itemList as $key => $item) {
            if($item['id'] === $lot['category_id']) {
                $itemList[$key]['isSelected'] = 'selected';
            } else {
                $itemList[$key]['isSelected'] = '';
            }
        }

        $navContent = renderTemplate('templates/nav-items.php', []);
        $mainContent = renderTemplate('templates/add-lot.php', ['navContent' => $navContent, 'itemList' => $itemList,
            'newLot' => $lot, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
        $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
            'footerContent' => $footerContent, 'title' => 'Добавление лота', 'mainClass' => '']);
    } else {
        $imagePath = 'img/' . uniqid('', TRUE) . '.' . pathinfo($_FILES['lot_image']['name'])['extension'];
        move_uploaded_file($_FILES['lot_image']['tmp_name'], $imagePath);
        $sql = 'insert into lots (name, category_id, description, initial_price, bet_increment, close_date, image) values (?,?,?,?,?,?,?)';
        $data = [$lot['name'], $lot['category_id'], $lot['description'], $lot['initial_price'], $lot['bet_increment'], $lot['close_date'], $imagePath];
        $stmt = db_get_prepare_stmt($connection, $sql, $data);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        $new_lot_id = mysqli_insert_id($connection);
        header("Location: lot.php?lot_id=$new_lot_id");
    }
} else {
    $navContent = renderTemplate('templates/nav-items.php', []);
    $mainContent = renderTemplate('templates/add-lot.php', ['navContent' => $navContent, 'itemList' => $itemList,
        'newLot' => ['name' => '', 'category_id' => 0, 'description' => '', 'initial_price' => '', 'bet_increment' => '',
            'close_date' => ''], 'form_error_class' => '']);
    $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
        'footerContent' => $footerContent, 'title' => 'Добавление лота', 'mainClass' => '']);
}

echo $layoutContent;

?>