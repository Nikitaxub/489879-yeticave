<div class="main-header__container container">
    <h1 class="visually-hidden">YetiCave</h1>
    <a class="main-header__logo" href="index.php">
        <img src="img/logo.svg" width="160" height="39" alt="Логотип компании YetiCave">
    </a>
    <form class="main-header__search" method="get" action="https://echo.htmlacademy.ru">
        <input type="search" name="search" placeholder="Поиск лота">
        <input class="main-header__search-btn" type="submit" name="find" value="Найти">
    </form>
    <?php if (isset($login['email'])): ?>
        <a class="main-header__add-lot button" href="add.php">Добавить лот</a>
    <?php endif; ?>

    <nav class="user-menu">
        <ul class="user-menu__list">
            <?php if (!isset($login['email'])): ?>
                <li class="user-menu__item">
                    <a href="sign-up.php">Регистрация</a>
                </li>
                <li class="user-menu__item">
                    <a href="login.php">Вход</a>
                </li>
            <?php else: ?>
                <div class="user-menu__image">
                    <img alt="oops" src="<?= $login['avatar']; ?>" width="40" height="40" alt="Пользователь">
                </div>
                <div class="user-menu__logged">
                    <p><?= $login['name']; ?></p>
                    <a href="logout.php">Выйти</a>
                </div>
            <?php endif; ?>
        </ul>
    </nav>
</div>