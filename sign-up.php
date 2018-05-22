<?php

require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', []);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$imagePath = '';

$errors = [];

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'];
$requiredFields = ['email', 'name', 'password', 'contacts'];

if (isPost('user')) {
    $user = $_POST['user'];

    foreach ($requiredFields as $field) {
        if (empty($user[$field])) {
            switch ($field) {
                case 'email':
                    $errors[$field] = 'Введите e-mail';
                    break;
                case 'password':
                    $errors[$field] = 'Введите пароль';
                    break;
                case 'name':
                    $errors[$field] = 'Введите имя';
                    break;
                case 'contacts':
                    $errors[$field] = 'Напишите как с вами связаться';
                    break;
            }
        }
    }

    if (empty($errors['email'])) {
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email должен быть корректным';
        } elseif (!empty(isUsedEmail($connection, $user['email']))) {
            $errors['email'] = 'Этот email уже используется';
        }
    }

    if (isset($_FILES['avatar']) and $fileSize = $_FILES['avatar']['size'] > 0 ) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileName = $_FILES['avatar']['tmp_name'];
        $fileType = finfo_file($finfo, $fileName);
        if (!in_array($fileType, $lotImageMIMETypes)) {
            $errors['avatar'] = 'Это не картинка';
        }
        finfo_close($finfo);
    }

    if (!empty($errors)) {
        $navContent = renderTemplate('templates/nav-items.php', []);
        $mainContent = renderTemplate('templates/sign-up.php', ['navContent' => $navContent,
            'user' => $user, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
        $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
            'footerContent' => $footerContent, 'title' => 'Регистрация', 'mainClass' => '']);
    } else {
        if ($fileSize > 0) {
            $imagePath = 'img/' . uniqid('', TRUE) . '.' . pathinfo($_FILES['avatar']['name'])['extension'];
            move_uploaded_file($_FILES['avatar']['tmp_name'], $imagePath);
        }
        $sql = 'insert into users (name, email, password_hash, avatar, contacts) values (?,?,?,?,?)';
        $data = [$user['name'], $user['email'], password_hash($user['password'],  PASSWORD_DEFAULT), $imagePath, $user['contacts']];
        db_execute_stmt ($connection, $sql, $data);

        header("Location: index.php");
    }
} else {
    $navContent = renderTemplate('templates/nav-items.php', []);
    $mainContent = renderTemplate('templates/sign-up.php', ['navContent' => $navContent, 'itemList' => $itemList, 'form_error_class' => '',
        'user' => ['name' => '', 'email' => '', 'password' => '', 'contacts' => '']]);
    $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
        'footerContent' => $footerContent, 'title' => 'Регистрация', 'mainClass' => '']);
}

echo $layoutContent;

?>