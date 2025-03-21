# ntfy.sh Error Notifier for Namingo Registry

This script checks for new high-severity errors from Namingo Registry and sends real-time push notifications via [ntfy.sh](https://ntfy.sh).

## ⚙️ Setup Steps

### 1. 📥 Download

Clone or copy the files to your server:

```bash
sudo mkdir -p /opt/registry/ntfy
cd /opt/registry/ntfy
# Add ntfy_error_notifier.php and ntfy_config.php here
```

### 2. 🛠 Configure

Edit `ntfy_config.php` with your database credentials and ntfy topic name:

```php
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'registry',
        'user' => 'youruser',
        'pass' => 'yourpass',
        'charset' => 'utf8mb4'
    ],
    'ntfy_topic' => 'https://ntfy.sh/my-channel', // Replace with your topic
    'min_level' => 400,
    'last_id_file' => __DIR__ . '/.last_id'
];
```

### 3. 🔔 Create an ntfy.sh Topic

- Visit [https://ntfy.sh](https://ntfy.sh)
- Choose a topic name (e.g., `my-channel`)
- Subscribe to it using the ntfy app (Android/iOS) or web

No registration required, but for private topics, consider [authentication options](https://docs.ntfy.sh/publish/#authentication).

### 4. 🧪 Test Run

Run manually to ensure everything works:

```bash
php /opt/registry/ntfy/ntfy_error_notifier.php
```

### 5. 🕒 Add to Cron

Open your crontab:

```bash
crontab -e
```

Add the following line to check for errors every minute:

```bash
* * * * * /usr/bin/php /opt/registry/ntfy/ntfy_error_notifier.php > /dev/null 2>&1
```

## 📄 License

MIT License