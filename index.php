<?php

require __DIR__ . '/initialize.php';
var_dump(PROJECT_ROOT);
$isIndex = true; //Используем в  main-header__logo в  layout-template.php
$title = 'Главная страница';
$lots = getLots($db);

echo renderTemplate('index-template.php', $title, $authUser, $categories, [
    'categories' => $categories,
    'lots' => $lots
], $isIndex);

