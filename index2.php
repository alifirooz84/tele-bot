<?php

// 1. توکن رباتت رو اینجا بذار
$bot_token = '7956714963:AAHnybhfhA3c0d7C1VJnXIHhbR-fkeTsXfI';
$api_url = "https://api.telegram.org/bot$bot_token/";

// 2. گرفتن داده ورودی از تلگرام
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// برای تست لاگ بفرستیم (اختیاری)
file_put_contents("log.txt", json_encode($update));

// 3. اگر پیام وجود داشت، بررسیش کن
if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];

    // اگر کاربر شماره تماسش رو فرستاد
    if (isset($message["contact"])) {
        $phone = $message["contact"]["phone_number"];
        sendPhoneToWordpress($phone);
        sendMessage($chat_id, "شماره شما با موفقیت ثبت شد ✅");
    }

    // اگر کاربر /start فرستاد
    if (isset($message["text"]) && $message["text"] === "/start") {
        sendKeyboard($chat_id);
    }
}

// 4. ارسال شماره به سایت وردپرس
function sendPhoneToWordpress($phone) {
    $url = 'https://pestehiran.shop/?receive-phone=1'; // آدرس هدف وردپرس

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

// 5. ارسال پیام ساده به کاربر
function sendMessage($chat_id, $text) {
    global $api_url;
    file_get_contents($api_url . "sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

// 6. نمایش کیبورد برای دریافت شماره
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
