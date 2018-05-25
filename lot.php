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

$errors = [];

$headerContent = renderTemplate('templates/header-common.php', ['login' => $_SESSION['login']]);
$footerContent = renderTemplate('templates/footer-common.php', ['itemList' => $itemList]);
$navContent = renderTemplate('templates/nav-items.php', []);

if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['cost'])) {
    $cost = $_POST['cost'];

    if (empty($cost)) {
        $errors['cost'] = 'Введите ставку';
    }

    $minBet = $lot[0]['min_bet'];
    if (empty($errors['cost']) && $minBet > $cost) {
        $formatCostMinBet = formatCost($minBet);
        $errors['cost'] = "Ставка не может быть меньше $formatCostMinBet";
    }

    if (!empty($errors)) {
        $mainContent = renderTemplate('templates/lot.php', ['navContent' => $navContent, 'lot' => $lot[0], 'betsList' => $betsList,
            'cost' => $cost, 'form_error_class' => 'form--invalid', 'errors' => $errors]);
    } else {
        $data = [$cost, $_SESSION['login']['id'], $lotId];
        dbInsertBet($connection, $data);
        header("Location: lot.php?lot_id=$lotId");
    }
} else {
    $mainContent = renderTemplate('templates/lot.php', ['navContent' => $navContent, 'lot' => $lot[0], 'betsList' => $betsList,
        'form_error_class' => '']);
}

$layoutContent = renderTemplate('templates/layout.php', ['headerContent' => $headerContent, 'mainContent' => $mainContent,
    'footerContent' => $footerContent, 'title' => $lot[0]['name'], 'mainClass' => '']);
echo $layoutContent;

?>

