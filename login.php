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
$requiredFields = ['email', 'password'];

$login = [];
$errors = [];
$user = [];
if (isPost('login')) {
    $login = $_POST['login'];

    foreach ($requiredFields as $field) {
        if (empty($login[$field])) {
            switch ($field) {
                case 'email':
                    $errors[$field] = 'Введите e-mail';
                    break;
                case 'password':
                    $errors[$field] = 'Введите пароль';
                    break;
            }
        }
    }

    if (empty($errors['email']) && !filter_var($login['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email должен быть корректным';
    }

    if (empty($errors['password']) && !checkAuth($connection, $login['email'], $login['password'])) {
        $errors['password'] = 'Вы ввели неверный email/пароль';
    }

    if (!empty($errors)) {
        $mainContent = renderTemplate('templates/login.php', ['navContent' => $navContent,
            'login' => $login, 'errors' => $errors]);
    } else {
        $user = getUser($connection, $login['email']);
        $_SESSION['login'] = $user[0];
        header("Location: index.php");
    }
} else {
    $mainContent = renderTemplate('templates/login.php', ['navContent' => $navContent,
        'login' => ['email' => '', 'password' => '']]);
}

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => 'Вход', 'mainClass' => '']);
echo $layoutContent;