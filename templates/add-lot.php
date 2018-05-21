<?= $navContent; ?>
  <form class="form form--add-lot container <?= $form_error_class; ?>" action="" method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
      <div class="form__item <?php if ($errors['name']) {echo 'form__item--invalid';} ?>">
        <label for="lot-name">Наименование</label>
        <input id="lot-name" type="text" name="newLot[name]" placeholder="Введите наименование лота" value="<?= htmlspecialchars ($newLot['name']); ?>">
        <span class="form__error"><?= $errors['name']; ?></span>
      </div>
      <div class="form__item <?php if ($errors['category_id']) {echo 'form__item--invalid';} ?>">
        <label for="category">Категория</label>
        <select id="category" name="newLot[category_id]" >
            <?php
            foreach($itemList as $itemNum => $item) {
                ?>
                <option <?php if (isset($item['isSelected']) && in_array($item['isSelected'], ['','selected'])) {
                    echo $item['isSelected'];
                }; ?> value="<?= $item['id']; ?>"><?= $item['name']; ?></option>'
                <?php
            }
            ?>
        </select>
        <span class="form__error"><?= $errors['category_id']; ?></span>
      </div>
    </div>
    <div class="form__item form__item--wide <?php if ($errors['description']) {echo 'form__item--invalid';} ?>">
      <label for="message">Описание</label>
      <textarea id="message" name="newLot[description]" placeholder="Напишите описание лота" ><?= htmlspecialchars ($newLot['description']); ?></textarea>
      <span class="form__error"><?= $errors['description']; ?></span>
    </div>
    <div class="form__item form__item--file <?php if ($errors['lot_image']) {echo 'form__item--invalid';} ?>">
      <label>Изображение</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" value="" name="lot_image">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
        <span class="form__error"><?= $errors['lot_image']; ?></span>
    </div>
    <div class="form__container-three">
      <div class="form__item form__item--small <?php if ($errors['initial_price']) {echo 'form__item--invalid';} ?>">
        <label for="lot-rate">Начальная цена</label>
        <input id="lot-rate" type="number" name="newLot[initial_price]" placeholder="0"  value="<?= htmlspecialchars ($newLot['initial_price']); ?>">
        <span class="form__error"><?= $errors['initial_price']; ?></span>
      </div>
      <div class="form__item form__item--small <?php if ($errors['bet_increment']) {echo 'form__item--invalid';} ?>">
        <label for="lot-step">Шаг ставки</label>
        <input id="lot-step" type="number" name="newLot[bet_increment]" placeholder="0"  value="<?= htmlspecialchars ($newLot['bet_increment']); ?>">
        <span class="form__error"><?= $errors['bet_increment']; ?></span>
      </div>
      <div class="form__item <?php if ($errors['close_date']) {echo 'form__item--invalid';} ?>">
        <label for="lot-date">Дата окончания торгов</label>
        <input class="form__input-date" id="lot-date" type="date" name="newLot[close_date]"  value="<?= htmlspecialchars ($newLot['close_date']); ?>">
        <span class="form__error"><?= $errors['close_date']; ?></span>
      </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
  </form>
