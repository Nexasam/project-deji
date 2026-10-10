<?php

return [
    'allow_sync_queue' => (bool) env('ALLOW_SYNC_QUEUE', false),
    'initial_admin' => [
        'email' => env('PLATFORM_ADMIN_EMAIL', 'admin@verifiedshortlet.test'),
        'password' => env('PLATFORM_ADMIN_PASSWORD', 'AdminPassword123!'),
    ],
    'settlement_approver' => [
        'email' => env('PLATFORM_APPROVER_EMAIL'),
        'password' => env('PLATFORM_APPROVER_PASSWORD'),
    ],
];
