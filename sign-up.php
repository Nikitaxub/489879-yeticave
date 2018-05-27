<?php

require('functions.php');
require('data.php');

if (!isAuthorized()) {
    $_SESSION['login'] = [];
}

$session = getSession();

$headerContent = renderTemplate('templates/header-common.php', ['login' => $session]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);
$navContent = renderTemplate('templates/nav-items.php', []);
$mainContent = '';

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png'];
$requiredFields = ['email', 'name', 'password', 'contacts'];

$errors = [];
$user = [];
$finfo = '';
$fileName = '';
$fileType = '';
$imagePath = '';
$data = [];
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
        $mainContent = renderTemplate('templates/sign-up.php', ['navContent' => $navContent,
            'user' => $user, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
    } else {
        if ($fileSize > 0) {
            $imagePath = 'img/' . uniqid('', TRUE) . '.' . pathinfo($_FILES['avatar']['name'])['extension'];
            move_uploaded_file($_FILES['avatar']['tmp_name'], $imagePath);
        }
        $data = [$user['name'], $user['email'], password_hash($user['password'],  PASSWORD_DEFAULT), $imagePath, $user['contacts']];
        dbInsertUser($connection, $data);

        header("Location: login.php");
    }
} else {
    $mainContent = renderTemplate('templates/sign-up.php', ['navContent' => $navContent, 'form_error_class' => '',
        'user' => ['name' => '', 'email' => '', 'password' => '', 'contacts' => '']]);
}

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Регистрация', 'mainClass' => '']);
echo $layoutContent;