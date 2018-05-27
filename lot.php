<?php

require('functions.php');
require('data.php');

if (ctype_digit($_GET['lot_id'])) {
    $lotId = intval($_GET['lot_id']);
} else {
    redirect404();
}

if (!isAuthorized()) {
    $_SESSION['login'] = [];
}

$lot = getLot($connection, $lotId);

if (!$lot[0]) {
    redirect404();
}

$betsList = getBetList($connection, $lotId);
$session = getSession();

$headerContent = renderTemplate('templates/header-common.php', ['login' => $session]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);
$navContent = renderTemplate('templates/nav-items.php', []);
$mainContent = '';

$bet = [];
$errors = [];
$minBet = 0;
$formatCostMinBet = 0;
$data = [];
if (isPost('bet')) {
    $bet = $_POST['bet'];

    if (empty($bet['cost'])) {
        $errors['cost'] = 'Введите ставку';
    }

    $minBet = $lot[0]['min_bet'];
    if (empty($errors['cost']) && $minBet > $bet['cost']) {
        $formatCostMinBet = ceil($minBet);
        $errors['cost'] = "Ставка не может быть меньше $formatCostMinBet";
    }

    if (!empty($errors)) {
        $mainContent = renderTemplate('templates/lot.php', ['navContent' => $navContent, 'lot' => $lot[0], 'betsList' => $betsList,
            'bet' => $bet, 'form_error_class' => 'form--invalid', 'errors' => $errors, 'session' => $session]);
    } else {
        $data = [$bet['cost'], $session['id'], $lotId];
        dbInsertBet($connection, $data);
        header("Location: lot.php?lot_id=$lotId");
    }
} else {
    $mainContent = renderTemplate('templates/lot.php', ['navContent' => $navContent, 'lot' => $lot[0], 'betsList' => $betsList,
        'form_error_class' => '', 'session' => $session]);
}

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => $lot[0]['name'], 'mainClass' => '']);
echo $layoutContent;

