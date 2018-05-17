<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <link href="css/normalize.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <?= $header_content; ?>
</header>

<main <?= $main_class; ?>>
    <?= $main_content; ?>
</main>

<footer class="main-footer">
    <?= $footer_content; ?>
</footer>

</body>
</html>