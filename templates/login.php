<?= $navContent; ?>
  <form class="form container" action="" method="post">
    <h2>Вход</h2>
    <div class="form__item <?php if ($errors['email']) {echo 'form__item--invalid';} ?>">
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="login[email]" placeholder="Введите e-mail" value="<?= htmlspecialchars ($login['email']); ?>">
      <span class="form__error"><?= $errors['email']; ?></span>
    </div>
    <div class="form__item form__item--last <?php if ($errors['password']) {echo 'form__item--invalid';} ?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password" name="login[password]" placeholder="Введите пароль" >
      <span class="form__error"><?= $errors['password']; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
  </form>