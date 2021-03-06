<?= $navContent; ?>
  <section class="lot-item container">
    <h2><?= htmlspecialchars($lot['name']); ?></h2>
    <div class="lot-item__content">
      <div class="lot-item__left">
        <div class="lot-item__image">
          <img src="<?= htmlspecialchars($lot['image']); ?>" width="730" height="548" alt="Сноуборд">
        </div>
        <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['category']); ?></span></p>
        <p class="lot-item__description"><?= htmlspecialchars($lot['description']); ?></p>
      </div>
      <div class="lot-item__right">
          <?php if (isAuthorized() && ($lot['author'] !== $session['email']) &&
              $lot['lastBet'] !== $session['email'] && strtotime($lot['close_date']) > strtotime('today')): ?>
            <div class="lot-item__state <?php if ($errors['cost']) {echo 'form__item--invalid';} ?>">
              <div class="lot-item__timer timer">
                  <?= htmlspecialchars(getRemainingTime($lot['close_date'])); ?>
              </div>
              <div class="lot-item__cost-state">
                <div class="lot-item__rate">
                  <span class="lot-item__amount">Текущая цена</span>
                  <span class="lot-item__cost"><?= formatCost(htmlspecialchars($lot['actual_price'])); ?></span>
                </div>
                <div class="lot-item__min-cost">
                  Мин. ставка <span><?= formatCostRub(htmlspecialchars($lot['min_bet'])); ?></span>
                </div>
              </div>
                <form class="lot-item__form" action="" method="post">
                        <div class="form__item lot-item__form-item">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="number" name="bet[cost]" value="<?= ceil(htmlspecialchars($lot['min_bet'])); ?>">
                            <span class="form__error"><?= $errors['cost']; ?></span>
                        </div>
                        <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
          <?php endif; ?>
        <div class="history">
          <h3>История ставок (<span>
                  <?php if (count($betsList) > 0 ) {
                      echo count($betsList);
                  }
                  else {
                      echo 'Ставок нет';
                  }?>
          </span>)</h3>
          <table class="history__list">
              <?php
              foreach($betsList as $betNum => $bet) {
                  ?>
                  <tr class="history__item">
                      <td class="history__name"><?= htmlspecialchars($bet['user_name']); ?></td>
                      <td class="history__price"><?= formatCostRub(htmlspecialchars($bet['price'])); ?></td>
                      <td class="history__time"><?= getPassingTime(htmlspecialchars($bet['create_date'])); ?></td>
                  </tr>
                  <?php
              }
              ?>
          </table>
        </div>
      </div>
    </div>
  </section>