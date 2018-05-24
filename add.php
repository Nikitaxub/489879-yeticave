<?php

session_start();
require('functions.php');
require('data.php');

if (!isset($_SESSION['email'])) {
    redirect403();
}

$headerContent = renderTemplate('templates/header-common.php', []);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$imagePath = '';

array_unshift ($itemList, ['id' => 0, 'name' => 'Выберите категорию']);

$errors = [];

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'];
$requiredFields = ['name', 'category_id', 'description', 'initial_price', 'bet_increment', 'close_date'];

if (isPost('newLot')) {
    $lot = $_POST['newLot'];

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

        $navContent = renderTemplate('templates/nav-items.php', []);
        $mainContent = renderTemplate('templates/add-lot.php', ['navContent' => $navContent, 'itemList' => $itemList,
            'newLot' => $lot, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
        $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
            'footerContent' => $footerContent, 'title' => 'Добавление лота', 'mainClass' => '']);
    } else {
        $imagePath = 'img/' . uniqid('', TRUE) . '.' . pathinfo($_FILES['lot_image']['name'])['extension'];
        move_uploaded_file($_FILES['lot_image']['tmp_name'], $imagePath);
        $data = [$lot['name'], $lot['category_id'], $lot['description'], $lot['initial_price'], $lot['bet_increment'], $lot['close_date'], $imagePath];
        dbInsertLot($connection, $data);

        $newLotId = mysqli_insert_id($connection);
        header("Location: lot.php?lot_id=$newLotId");
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