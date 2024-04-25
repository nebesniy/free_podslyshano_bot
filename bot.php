<?php

// Получение данных из переменных окружения
$token = getenv('TELEGRAM_BOT_TOKEN');
$destination = getenv('DESTINATION_CHANNEL'); // Имя канала для пересылки с собачкой в начале, например @channel. У бота должен быть доступ к каналу с правом публикации сообщений и медиафайлов.

// Проверка наличия необходимых данных
if (empty($token) || empty($destination)) {
    die('Необходимо настроить токен бота и канал назначения');
}

$path = "https://api.telegram.org/bot".$token;

// Получение данных о сообщении
$update = json_decode(file_get_contents("php://input"), TRUE);
$chatId = $update["message"]["chat"]["id"];
$fileId = isset($update["message"]["photo"][0]["file_id"]) ? $update["message"]["photo"][0]["file_id"] : null;
$message = $update["message"]["text"];
$caption = isset($update["message"]["caption"]) ? $update["message"]["caption"] : null;

// Формирование текста для отправки
if (strpos($message, "/start") === 0) {
    $text = urlencode("Гамарджоба! Я бот свободного подслушано. Настроен автоматический постинг в " . $destination);
} else {
    $text = $caption ? urlencode($caption) : urlencode($message);
}

// Отправка сообщения в зависимости от типа сообщения
if ($fileId) {
    sendPhoto($path, $destination, $fileId, $text);
} else {
    sendMessage($path, $destination, $text);
}

// Функция отправки сообщения с фото
function sendPhoto($path, $destination, $fileId, $text) {
    $url = sprintf("%s/sendphoto?chat_id=%s&photo=%s&caption=%s", $path, $destination, $fileId, $text);
    sendRequest($url);
}

// Функция отправки текстового сообщения
function sendMessage($path, $destination, $text) {
    $url = sprintf("%s/sendmessage?chat_id=%s&text=%s", $path, $destination, $text);
    sendRequest($url);
}

// Функция отправки запроса к API Telegram
function sendRequest($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Обработка ошибок
    if (!$response) {
        error_log("Ошибка при отправке запроса: " . curl_error($ch));
    }
}
