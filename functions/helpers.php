<?php
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template(string $name, array $data = []): string
{
    $name = PROJECT_ROOT . '/templates/' . $name;
    ob_start();
    extract($data);
    require $name;
    return ob_get_clean();
}


/**
 * @param string $expire_at
 * @return array
 */
function getDateDiff(string $expire_at): array
{
    $period = strtotime($expire_at) - time();
    $hours = floor($period / 3600);
    $minutes = 60 - date('i');

    $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
    $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);

    return [
        'hours' => $hours,
        'minutes' => $minutes
    ];
}


/**
 * @param int $price
 * @return string
 */
function getPrice(int $price): string
{
    $price = ceil($price);
    return number_format($price, 0, '', ' ') . ' ₽';
}




/**
 * Преобразует специальные символы в HTML-сущности
 * @param string $str
 * @return string
 */
function esc(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES);
}


/**
 * @param string $name
 * @param array $data
 * @return string
 */
function  renderTemplate(string $name, string $title, array|string $authUser, array $categories, array $data = [], string $searchQuery = '', bool $isIndex = false): string
{
    $main = include_template($name, $data);
    return include_template('layout-template.php', [
        'title' => $title,
        'isIndex' => $isIndex,
        'authUser' => $authUser,
        'categories' => $categories,
        'searchQuery' => $searchQuery,
        'main' => $main,
    ]);
}


/**
 * @param array $categories
 * @param int $responseCode
 * @param string $errMessage
 * @var $message
 */
#[NoReturn] function httpError(array $categories, array $authUser, int $responseCode,  string $errMessage = '' )
{
    $error = [
        403 => '403 - У вас нет права зайти на страницу ',
        404 => '404 - Данной страницы не существует на сайте',
    ];

        $title = $error[$responseCode];
        $message = $errMessage;

    http_response_code($responseCode);
    echo renderTemplate('404-template.php', $title, $authUser, $categories, [
        'categories' => $categories,
        'title' => $title,
        'message'=> $message,
         ]
    );
    exit;
}


/**
 * @param $length
 * @return string
 */
function randomString($length): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($characters), 0, $length);
}

/**
 * @param array $submittedFile
 * @return string
 */
function uploadFile(array $submittedFile, string $uploadPath) : string
{
    if (is_uploaded_file($submittedFile['tmp_name'])) {
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $fileName = randomString(10) .'-'. $submittedFile['name'];
        $filePath = PROJECT_ROOT . '/'. $uploadPath . '/' . $fileName;//
        move_uploaded_file($submittedFile['tmp_name'], $filePath);
        return  '/'. $uploadPath  .'/'. $fileName;
    }
}

#[Pure] function betDateFormat(string $betDateTime): string
{
    $dateNow = date_create();
    $dateBetCreated = date_create($betDateTime);

    //считает разницу с текущим временем
    $dateDifference = date_diff($dateBetCreated, $dateNow);
    $days = $dateDifference->d;// Количество дней int
    $hours = $dateDifference->h;// Количество часов int
    $minutes = $dateDifference->i;// Количество минут int
    $seconds = $dateDifference->s ?: 1;// Количество секунд int

    if($days == 1){
        return date('Вчера, в H:i',  strtotime($betDateTime));
    } elseif($days){
        return date('d.m.y в H:i',  strtotime($betDateTime));
    } elseif ($hours) {
        $date = sprintf("%s %s назад", $hours, get_noun_plural_form($hours, 'час', 'часа', 'часов'));
    } elseif ($minutes) {
        $date = sprintf("%s %s назад", $minutes, get_noun_plural_form($minutes, 'минута', 'минуты', 'минут'));
    } elseif ($seconds) {
        $date = sprintf("%s %s назад", $seconds, get_noun_plural_form($seconds, 'секунда', 'секунды', 'секунд'));
    }
    return $date;
}
