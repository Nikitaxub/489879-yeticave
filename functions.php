<?php
function formatCost ($value) {
    return number_format(ceil($value), 0, '', ' ').'  ₽';
}

function renderTemplate($filename, $parameters = []) {
    if (is_file($filename) && is_readable($filename)) {
        ob_start();
        extract($parameters);
        require($filename);
        return ob_get_clean();
    } else {
        return '';
    }
}
function getRemainingTime() {
    date_default_timezone_set('Europe/Moscow');
    $remainingTimeInSec = strtotime( 'tomorrow') - time();
    return intdiv($remainingTimeInSec, 3600).':'.intdiv($remainingTimeInSec % 3600,  60);
}
?>