<?php

require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', ['user_avatar' => $user_avatar, 'user_name' => $user_name,
    'is_auth' => $is_auth]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$image_path = '';

array_unshift ($itemList, ['id' => 0, 'name' => 'Выберите категорию']);

$errors = [];
$item_error_classes = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST['newLot'];

    $required_fields = ['name', 'category_id', 'description', 'initial_price', 'bet_increment', 'close_date'];

    foreach ($required_fields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Заполните это поле';
            $item_error_classes[$field] = 'form__item--invalid';
        }
    }

    if ($lot['category_id'] == 0) {
        $errors['category_id'] = 'Категория не выбрана';
        $item_error_classes['category_id'] = 'form__item--invalid';
    }

    foreach ($lot as $key => $value) {
        if ($key == "initial_price" || $key == "bet_increment") {
            if (ctype_digit($value)) {
                $lot[$key] = intval($value);
            } elseif (!$errors[$key]) {
                $errors[$key] = 'Поле должно содержать только цифры';
                $item_error_classes[$key] = 'form__item--invalid';
            }
        }
    }

    if (isset($_FILES['lot_image'])) {
        if (is_uploaded_file($_FILES['lot_image']['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_name = $_FILES['lot_image']['tmp_name'];
            $file_size = $_FILES['lot_image']['size'];
            $file_type = finfo_file($finfo, $file_name);
            if ($file_type !== 'image/jpeg' && $file_type !== 'image/png') {
                $errors['lot_image'] = 'Картинка не в формате jpeg/png';
                $item_error_classes['lot_image'] = 'form__item--invalid';
            }
            finfo_close($finfo);
        } else {
            $errors['lot_image'] = 'Загрузите картинку в формате jpeg/png';
            $item_error_classes['lot_image'] = 'form__item--invalid';
        }
    }

    if (count($errors)) {
        foreach ($itemList as $key => $item) {
            if($item['id'] === $lot['category_id']) {
                $itemList[$key]['isSelected'] = 'selected';
            } else {
                $itemList[$key]['isSelected'] = '';
            }
        }

        $nav_content = renderTemplate('templates/nav-items.php', []);
        $mainContent = renderTemplate('templates/add-lot.php', ['nav_content' => $nav_content, 'itemList' => $itemList,
            'newLot' => $lot, 'form_error_class' => 'form--invalid', 'errors' => $errors, 'item_error_classes' => $item_error_classes]);
        $layoutContent = renderTemplate('templates/layout.php', ['header_content' => $headerContent, 'main_content' => $mainContent,
            'footer_content' => $footerContent, 'title' => 'Добавление лота', 'main_class' => '']);
    } else {
        $image_path = $file_path . uniqid('', TRUE) . '.' . pathinfo($_FILES['lot_image']['name'])['extension'];
        move_uploaded_file($_FILES['lot_image']['tmp_name'], $image_path);
        $stmt = mysqli_stmt_init($connection);
        $sql = 'insert into lots (name, category_id, description, initial_price, bet_increment, close_date, image) values (?,?,?,?,?,?,?)';

        if (mysqli_stmt_prepare($stmt, $sql)) {

            mysqli_stmt_bind_param($stmt, "sisiiss",
                $lot['name'],$lot['category_id'], $lot['description'], $lot['initial_price'], $lot['bet_increment'], $lot['close_date'], $image_path);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            $new_lot_id = mysqli_insert_id($connection);
            header("Location: lot.php?lot_id=$new_lot_id");
        }
    }
} else {
    foreach ($itemList as $key => $item) {
        if($item['id'] === '0') {
            $itemList[$key]['isSelected'] = 'selected';
        } else {
            $itemList[$key]['isSelected'] = '';
        }
    }

    $nav_content = renderTemplate('templates/nav-items.php', []);
    $mainContent = renderTemplate('templates/add-lot.php', ['nav_content' => $nav_content, 'itemList' => $itemList,
        'newLot' => ['name' => '', 'category_id' => 0, 'description' => '', 'initial_price' => '', 'bet_increment' => '',
            'close_date' => ''], 'form_error_class' => '']);
    $layoutContent = renderTemplate('templates/layout.php', ['header_content' => $headerContent, 'main_content' => $mainContent,
        'footer_content' => $footerContent, 'title' => 'Добавление лота', 'main_class' => '']);
}

echo $layoutContent;

?>