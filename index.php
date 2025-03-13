<?php
require_once('settings.php');

// Dosya kontrolü
if (!file_exists(ENTRIES_FILE)) {
    file_put_contents(ENTRIES_FILE, '[]');
}

$entries = json_decode(file_get_contents(ENTRIES_FILE), true) ?: [];

// POST işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($content)) {
        $newEntry = [
            'date' => date('Y-m-d H:i:s'),
            'content' => nl2br(htmlspecialchars($content))
        ];
        array_unshift($entries, $newEntry);
        file_put_contents(ENTRIES_FILE, json_encode($entries, JSON_PRETTY_PRINT));
        header("Location: ?admin&success");
        exit;
    } else {
        header("Location: ?admin&error=1");
        exit;
    }
}

$error = $_GET['error'] ?? '';
$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Açık Günlük</title>
    <style>
        body { max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; }
        .entry { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .date { color: #666; font-size: 0.9em; }
        textarea { width: 100%; height: 150px; margin: 10px 0; padding: 10px; }
        .error { color: red; margin: 10px 0; }
        .success { color: green; margin: 10px 0; }
    </style>
</head>
<body>
    <?php if(isset($_GET['admin'])): ?>
        <!-- Yeni Giriş Formu -->
        <h2>Yeni Günlük Girişi</h2>
        
        <?php if($error): ?>
            <div class="error">Hata: İçerik boş olamaz!</div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success">Başarıyla kaydedildi!</div>
        <?php endif; ?>
        
        <form method="POST">
            <textarea 
                name="content" 
                placeholder="Bugün neler oldu..."
                required></textarea>
            <button type="submit">Yayınla</button>
        </form>
        <p><a href="?">← Tüm Kayıtları Görüntüle</a></p>
    
    <?php else: ?>
        <!-- Ana Sayfa -->
        <h1>Herkese Açık Günlük</h1>
        
        <?php if(empty($entries)): ?>
            <p>Henüz kayıt bulunmamaktadır.</p>
        <?php else: ?>
            <?php foreach($entries as $entry): ?>
                <div class="entry">
                    <div class="date"><?= $entry['date'] ?></div>
                    <div class="content"><?= $entry['content'] ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="margin-top: 50px; text-align: center;">
            <a href="?admin">Yeni Kayıt Ekle</a>
        </div>
    <?php endif; ?>
</body>
</html>
