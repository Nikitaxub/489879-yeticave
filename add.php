<?php

require('functions.php');
require('data.php');

if (!isAuthorized()) {
    redirect403();
}
$session = getSession();

$headerContent = renderTemplate('templates/header-common.php', ['login' => $session]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);
$navContent = renderTemplate('templates/nav-items.php', []);
$mainContent = '';

array_unshift ($itemList, ['id' => 0, 'name' => 'Выберите категорию']);

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png'];
$requiredFields = ['name', 'category_id', 'description', 'initial_price', 'bet_increment', 'close_date'];

$lot = [];
$errors = [];
$fileSize = 0;
$finfo = '';
$fileName = '';
$fileType = '';
$imagePath = '';
$data = [];
$newLotId = 0;
if (isPost('newLot')) {
    $lot = $_POST['newLot'];

    foreach ($requiredFields as $field) {
        if (empty($lot[$field]) ) {
            $errors[$field] = 'Заполните это поле';
        }
    }

    if ($lot['category_id'] == 0) {
        $errors['category_id'] = 'Категория не выбрана';
    }

    if (is_numeric($lot['initial_price']) && $lot['initial_price'] > 0) {
        $lot['initial_price'] = intval($lot['initial_price']);
    } else {
        $errors['initial_price'] = 'Число должно быть больше нуля';
    }

    if (ctype_digit($lot['bet_increment']) && $lot['bet_increment'] > 0) {
        $lot['bet_increment'] = intval($lot['bet_increment']);
    } else {
        $errors['bet_increment'] = 'Число должно быть целым и больше нуля';
    }

    if ((strtotime($lot['close_date']) < strtotime('+1 day')) && empty($errors['close_date'])) {
        $errors['close_date'] = 'Торги не могут длится менее 24 часов';
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
            $errors['lot_image'] = 'Загрузите картинку в формате jpeg/png';
        }
    }

    if (!empty($errors)) {
        $mainContent = renderTemplate('templates/add-lot.php', ['navContent' => $navContent, 'itemList' => $itemList,
            'newLot' => $lot, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
    } else {
        $imagePath = 'img/' . uniqid('', TRUE) . '.' . pathinfo($_FILES['lot_image']['name'])['extension'];
        move_uploaded_file($_FILES['lot_image']['tmp_name'], $imagePath);
        $data = [$lot['name'], $lot['category_id'], $lot['description'], $lot['initial_price'], $lot['bet_increment'], $lot['close_date'], $imagePath, $session['id']];
        dbInsertLot($connection, $data);
        $newLotId = mysqli_insert_id($connection);
        header("Location: lot.php?lot_id=$newLotId");
    }
} else {
    $mainContent = renderTemplate('templates/add-lot.php', ['navContent' => $navContent, 'itemList' => $itemList,
        'newLot' => ['name' => '', 'category_id' => 0, 'description' => '', 'initial_price' => '', 'bet_increment' => '',
            'close_date' => ''], 'form_error_class' => '']);
}

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Добавление лота', 'mainClass' => '']);
echo $layoutContent;