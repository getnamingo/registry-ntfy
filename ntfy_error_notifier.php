<?php

$config = require __DIR__ . '/ntfy_config.php';

try {
    // Setup DB
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}";
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Track last ID
    $lastIdFile = $config['last_id_file'];
    $lastId = file_exists($lastIdFile) ? (int)file_get_contents($lastIdFile) : 0;

    // Fetch new severe logs
    $stmt = $pdo->prepare("
        SELECT id, level, level_name, message, created_at 
        FROM error_log 
        WHERE id > :last_id AND level >= :min_level 
        ORDER BY id ASC
    ");
    $stmt->execute([
        ':last_id' => $lastId,
        ':min_level' => $config['min_level']
    ]);

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$logs) {
        exit(0); // Nothing new
    }

    foreach ($logs as $log) {
        $text = "[{$log['created_at']}] [{$log['level_name']}] {$log['message']}";
        sendNtfy($config['ntfy_topic'], $text, $log['level']);
        $lastId = $log['id'];
    }

    // Update last ID
    file_put_contents($lastIdFile, $lastId);
} catch (Throwable $e) {
    // Log to system log or email in real production use
    error_log("ntfy_error_notifier failed: " . $e->getMessage());
    exit(1);
}

// ntfy.sh notifier
function sendNtfy(string $topic, string $message, int $level): void {
    $priority = $level >= 500 ? 'urgent' : ($level >= 400 ? 'high' : 'default');
    $title = $level >= 500 ? '🚨 Critical Error' : '⚠️ Error';

    $ch = curl_init($topic);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $message,
        CURLOPT_HTTPHEADER => [
            "Title: $title",
            "Priority: $priority"
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5
    ]);
    curl_exec($ch);
    curl_close($ch);
}