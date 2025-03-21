<?php
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'registry',
        'user' => 'youruser',
        'pass' => 'yourpass',
        'charset' => 'utf8mb4'
    ],
    'ntfy_topic' => 'https://ntfy.sh/my-channel', // Your ntfy.sh topic URL
    'min_level' => 400, // Minimum level to report (400=ERROR, 500=CRITICAL)
    'last_id_file' => __DIR__ . '/.last_id'
];