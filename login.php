<?php

require('functions.php');
require('data.php');

$headerContent = renderTemplate('templates/header-common.php', []);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);

$errors = [];

$lotImageMIMETypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'];
$requiredFields = ['email', 'password'];

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
        $errors['password'] = 'Вы ввели неверный пароль';
    }

    if (!empty($errors)) {
        $navContent = renderTemplate('templates/nav-items.php', []);
        $mainContent = renderTemplate('templates/login.php', ['navContent' => $navContent,
            'login' => $login, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
        $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
            'footerContent' => $footerContent, 'title' => 'Вход', 'mainClass' => '']);
    } else {
        session_start();
        $_SESSION['email'] = $login['email'];
        $user = getUser($connection, $login['email']);
        $_SESSION['name'] = $user[0]['name'];
        $_SESSION['avatar'] = $user[0]['avatar'];
        header("Location: index.php");
    }
} else {
    $navContent = renderTemplate('templates/nav-items.php', []);
    $mainContent = renderTemplate('templates/login.php', ['navContent' => $navContent, 'form_error_class' => '',
        'login' => ['email' => '', 'password' => '']]);
    $layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
        'footerContent' => $footerContent, 'title' => 'Вход', 'mainClass' => '']);
}

echo $layoutContent;

?>