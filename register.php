<?php

require __DIR__ . '/initialize.php';

$title = 'Регистрация';

if ($authUser) {
    httpError($categories, 403, HEADER_USER_REGISTER_ERR);
}

$formErrors = [];
$submittedData = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    var_dump($_SESSION['token']);


    // этап 1: принять все данные формы:
    $submittedData = [
        'user-email' => trim(filter_input(INPUT_POST, 'user-email')),
        'user-password' => trim(filter_input(INPUT_POST, 'user-password')),
        'user-name' =>trim(filter_input(INPUT_POST, 'user-name')),
        'user-message' => trim(filter_input(INPUT_POST, 'user-message')),
    ];

    // этап 2: проверить данные запроса:
    $formErrors = [
        'user-email' => validateEmail(
            $submittedData['user-email'],
            EMPTY_EMAIL_ERR,
            REGISTER_INVALID_EMAIL_ERR
        ) ?? isUserEmailExists(
            $submittedData['user-email'],
            $db,
            REGISTER_EXIST_EMAIL_ERR),
        'user-password' => validateText(
            $submittedData['user-password'],
            REGISTER_PASSWORD_EXIST_ERR,
            true,
            REGISTER_PASSWORD_MIN_LENGTH,
            REGISTER_PASSWORD_MIN_LENGTH_ERR,
            REGISTER_PASSWORD_MAX_LENGTH,
            REGISTER_PASSWORD_MAX_LENGTH_ERR
        ),
        'user-name' => validateText(
            $submittedData['user-name'],
            REGISTER_NAME_EXIST_ERR,
            true,
            REGISTER_NAME_MIN_LENGTH,
            REGISTER_NAME_MIN_LENGTH_ERR,
            REGISTER_NAME_MAX_LENGTH,
            REGISTER_NAME_MAX_LENGTH_ERR
        ),
        'user-message' => validateText(
            $submittedData['user-message'],
            REGISTER_MESSAGE_EXIST_ERR,
            true,
            REGISTER_MESSAGE_MIN_LENGTH,
            REGISTER_MESSAGE_MIN_LENGTH_ERR,
            REGISTER_MESSAGE_MAX_LENGTH,
            REGISTER_MESSAGE_MAX_LENGTH_ERR
        ),
    ];
    $formErrors=array_filter($formErrors);

    // этап 3: сохранить проверенные данные если соответствует правилам валидации:
    if (count($formErrors) === 0) {
        $submittedData['user-password'] = password_hash($submittedData['user-password'], PASSWORD_DEFAULT);
        saveUser($db, $submittedData);
        header("Location: login.php");
        exit;
    }
}

echo renderTemplate(
    'register-template.php', $title, $authUser, $categories, [
                          'categories' => $categories,
                          'formErrors' => $formErrors,
                          'submittedData' =>  $submittedData,
                      ]
);

