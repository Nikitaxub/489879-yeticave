<?= $navContent; ?>
  <form class="form container <?= $form_error_class; ?>" action="" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?php if ($errors['email']) {echo 'form__item--invalid';} ?>">
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?= htmlspecialchars ($user['email']); ?>">
      <span class="form__error"><?= $errors['email']; ?></span>
    </div>
    <div class="form__item <?php if ($errors['password']) {echo 'form__item--invalid';} ?>">
      <label for="password">Пароль*</label>
      <input id="password" type="text" name="user[password]" placeholder="Введите пароль" value="">
      <span class="form__error"><?= $errors['password']; ?></span>
    </div>
    <div class="form__item <?php if ($errors['name']) {echo 'form__item--invalid';} ?>">
      <label for="name">Имя*</label>
      <input id="name" type="text" name="user[name]" placeholder="Введите имя" value="<?= htmlspecialchars ($user['name']); ?>">
      <span class="form__error"><?= $errors['name']; ?></span>
    </div>
    <div class="form__item <?php if ($errors['contacts']) {echo 'form__item--invalid';} ?>">
      <label for="message">Контактные данные*</label>
      <textarea id="message" name="user[contacts]" placeholder="Напишите как с вами связаться" ><?= htmlspecialchars ($user['contacts']); ?></textarea>
      <span class="form__error"><?= $errors['contacts']; ?></span>
    </div>
    <div class="form__item form__item--file form__item--last <?php if ($errors['avatar']) {echo 'form__item--invalid';} ?>">
      <label>Аватар</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" value="" name="avatar">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
        <span class="form__error"><?= $errors['avatar']; ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
  </form>