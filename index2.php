<?php

$bot_token = "7956714963:AAHnybhfhA3c0d7C1VJnXIHhbR-fkeTsXfI"
$api_url = "https://api.telegram.org/bot$bot_token/";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];

    // اگر کاربر شماره ارسال کرد
    if (isset($message["contact"])) {
        $phone = $message["contact"]["phone_number"];
        sendPhoneToWordpress($phone);
        sendMessage($chat_id, "شماره شما با موفقیت ثبت شد ✅");
    }

    // اگر کاربر نوشت /start
    if (isset($message["text"]) && $message["text"] === "/start") {
        sendKeyboard($chat_id);
    }
}

// تابع ارسال شماره به وردپرس
function sendPhoneToWordpress($phone) {
    $url = 'https://pestehiran.shop/?receive-phone=1'; // 👈 آدرس سایت خودت

    $data = ['phone' => $phone];
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ]
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// ارسال پیام متنی به کاربر
function sendMessage($chat_id, $text) {
    global $api_url;
    file_get_contents($api_url . "sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

// کیبورد برای درخواست شماره
function sendKeyboard($chat_id) {
    global $api_url;

    $keyboard = [
        "keyboard" => [
            [
                ["text" => "📞 ارسال شماره من", "request_contact" => true]
            ]
        ],
        "resize_keyboard" => true,
        "one_time_keyboard" => true
    ];

    $data = [
        'chat_id' => $chat_id,
        'text' => "لطفاً دکمه زیر را بزنید تا شماره شما ثبت شود:",
        'reply_markup' => json_encode($keyboard)
    ];

    $url = $api_url . "sendMessage?" . http_build_query($data);
    file_get_contents($url);
}
