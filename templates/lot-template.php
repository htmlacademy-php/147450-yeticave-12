<main>
    <nav class="nav">
        <ul class="nav__list container">
            <!--заполните этот список из массива категорий-->
            <?php
            foreach ($categories as $category) : ?>
                <li class="nav__item">
                    <a href="/all-lots.php?category=<?= esc($category['category_name']) ?>"><?= esc($category['category_name']) ?></a>
                </li>
            <?php
            endforeach;
            ?>
        </ul>
    </nav>
    <section class="lot-item container">
        <h2><?= esc($lot['lot_name']) ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= esc($lot['lot_img']) ?>" width="730" height="548" alt="Сноуборд">
                </div>
                <p class="lot-item__category">Категория: <span><?= esc($lot['category_name']) ?></span></p>
                <p class="lot-item__description"><?= esc($lot['lot_desc']) ?></p>
            </div>
            <div class="lot-item__right">
                <?php $data = getDateDiff($lot['lot_end']) ?>
                <div class="lot-item__state">
                    <?php if ($data['hours'] >= 0) : ?>
                    <div class="lot-item__timer timer <?= ($data['hours'] <= 0) ? 'timer--finishing' : ''; ?>">
                        <?= esc($data['hours']) . ':' . esc($data['minutes']) ?>
                    </div>
                        <div class="lot-item__cost-state">
                            <div class="lot-item__rate">
                                <span class="lot-item__amount">Текущая цена</span>
                                <span class="lot-item__cost"><?= esc(getPrice($currentPrice)) ?></span>
                            </div>
                            <div class="lot-item__min-cost">
                                Мин. ставка <span><?= esc(getPrice($minBetStep)) ?></span>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="timer  timer--end">Торги окончены в  <?= esc($lot['lot_end']) ?></div>
                    <?php endif; ?>
                        <?php if ($showBetForm) : ?>
                            <form class="lot-item__form"
                                  action="/lot.php?lot_id=<?= esc($lot['lot_id']) ?>" method="post" autocomplete="off">
                                <p class="lot-item__form-item form__item <?= count($formErrors) > 0 ? 'form__item--invalid' : '' ?>">
                                    <label for="cost">Ваша ставка</label>
                                    <input id="cost" type="text" name="cost"
                                           placeholder="<?= esc(getPrice($minBetStep)) ?>"
                                           value="<?= esc($submittedData['cost'] ?? '') ?>"
                                    >
                                    <span class="form__error"><?= esc($formErrors['cost'] ?? '') ?></span>
                                </p>
                                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?? '' ?>">
                                <button type="submit" class="button">Сделать ставку</button>
                            </form>
                        <?php endif; ?>
                    </div>

                <div class="history">
                    <?php if (count($bets) > 0) : ?>
                        <h3>История ставок (<span><?= esc(count($bets)); ?></span>)</h3>
                        <table class="history__list">
                            <?php foreach ($bets as $bet): ?>
                                <tr class="history__item">
                                    <td class="history__name"><?= esc($bet['user_name']); ?></td>
                                    <td class="history__price"><?= esc(getPrice($bet['bet_price'])); ?></td>
                                    <td class="history__time"><?= esc(betDateFormat($bet['bet_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <?php else: ?>
                        <h3>Ставок пока еще нет.</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

