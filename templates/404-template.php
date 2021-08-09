<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php
            foreach ($categories as $category) : ?>
                <li class="nav__item">
                    <a href="all-lots.html"><?= esc($category['category_name']) ?></a>
                </li>
            <?php
            endforeach; ?>
        </ul>
    </nav>
    <section class="lot-item container">
        <h2> <?=$title?> </h2>
        <h3> <?=$message?> </h3>
    </section>
</main>
