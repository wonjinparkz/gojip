<?php

return [
    'api_key' => env('ALIGO_API_KEY'),
    'user_id' => env('ALIGO_USER_ID'),
    'sender'  => env('ALIGO_SENDER'),
    'is_test' => env('ALIGO_IS_TEST', true),
    'base_url' => env('ALIGO_BASE_URL', 'https://apis.aligo.in'),
    'timeout' => env('ALIGO_TIMEOUT', 10),
];
