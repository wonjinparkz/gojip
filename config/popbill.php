<?php

return [
    'link_id' => env('POPBILL_LINK_ID'),
    'secret_key' => env('POPBILL_SECRET_KEY'),
    'is_test' => env('POPBILL_IS_TEST', true),
    'ip_restrict' => env('POPBILL_IP_RESTRICT', true),
    'use_static_ip' => env('POPBILL_USE_STATIC_IP', false),
    'use_ga_ip' => env('POPBILL_USE_GA_IP', false),
    'use_local_time' => env('POPBILL_USE_LOCAL_TIME', true),
];
