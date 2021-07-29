<?php
declare(strict_types=1);
require __DIR__ . '/initialize.php';
$title = 'Вход';


if ($authUser) {
    httpError($categories, 403, HEADER_USER_REGISTER_ERR);
}

$formErrors = [];
$submittedData = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['token'] !== $_POST['token']) {
        httpError($categories, 403);
    }


    // этап 1: принять все данные формы:
    $submittedData = [
        'user-email' => trim(filter_input(INPUT_POST, 'user-email')),
        'user-password' => trim(filter_input(INPUT_POST, 'user-password')),
    ];

    // этап 2: проверить данные запроса:
    $formErrors['user-email'] =
        validateEmail(
            $submittedData['user-email'],
            EMPTY_EMAIL_ERR,
            INVALID_EMAIL_ERR,
            true
        ) ?? isUserEmailExists(
            $submittedData['user-email'],
            $db,
            '',
            NO_EXIST_EMAIL_ERR
        );
    $formErrors['user-password'] = validateText(
            $submittedData['user-password'],
            NO_PASSWORD_ERR,
            true
        ) ?? isUserPasswordExists(
            $submittedData['user-email'],
            $submittedData['user-password'],
            $formErrors['user-email'],
            $db,
            NO_EXIST_PASSWORD_ERR
        );


    $formErrors=array_filter($formErrors);

    // этап 3: сохранить проверенные данные если соответствует правилам валидации:
  if (count($formErrors) === 0) {
      $user = getUserByEmail($db, $submittedData['user-email']);
      session_regenerate_id(true);
      $_SESSION['authUser'] = $user;
      header("Location: / ");
      exit;
   }
}

echo renderTemplate(
    'login-template.php', $title, $authUser, $categories, [
                               'categories' => $categories,
                               'formErrors' => $formErrors,
                               'submittedData' =>  $submittedData,
                           ]
);
