<?php
$tempDir = 'temp/';

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

$action = $_GET['action'] ?? '';
$file = $_GET['file'] ?? '';

if ($action === 'download' && $file) {
    $filePath = $tempDir . basename($file);
    if (file_exists($filePath)) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($filePath);
        exit;
    }
}

if ($action === 'delete' && $file) {
    $filePath = $tempDir . basename($file);
    if (file_exists($filePath)) {
        unlink($filePath);
        header('Location: filemanager.php');
        exit;
    }
}

$files = array_diff(scandir($tempDir), array('.', '..'));
$files = array_filter($files, function($f) {
    return pathinfo($f, PATHINFO_EXTENSION) === 'txt';
});
usort($files, function($a, $b) use ($tempDir) {
    return filemtime($tempDir . $b) - filemtime($tempDir . $a);
});
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staj Başvuru Formu</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .file-manager-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .file-manager-header a {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .file-manager-header a:hover {
            background-color: #2980b9;
        }
        .file-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .file-table th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .file-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .file-table tr:hover {
            background-color: #f5f5f5;
        }
        .file-actions {
            display: flex;
            gap: 10px;
        }
        .btn-download, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-download {
            background-color: #27ae60;
            color: white;
        }
        .btn-download:hover {
            background-color: #219150;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="file-manager-header">
        <h2>📁 Başvuru Dosya Yöneticisi</h2>
        <a href="index.php">← Yeni Başvuru</a>
    </div>

    <?php if (empty($files)): ?>
        <div class="empty-message">
            <p>Henüz kaydedilmiş başvuru dosyası yok.</p>
            <p><a href="index.php">Yeni başvuru oluşturmak için tıklayın</a></p>
        </div>
    <?php else: ?>
        <table class="file-table">
            <thead>
                <tr>
                    <th>Dosya Adı</th>
                    <th>Boyut</th>
                    <th>Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): 
                    $filePath = $tempDir . $file;
                    $fileSize = filesize($filePath);
                    $fileDate = date('d.m.Y H:i:s', filemtime($filePath));
                    $fileSizeKB = round($fileSize / 1024, 2);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($file); ?></td>
                    <td><?php echo $fileSizeKB; ?> KB</td>
                    <td><?php echo $fileDate; ?></td>
                    <td>
                        <div class="file-actions">
                            <a href="filemanager.php?action=download&file=<?php echo urlencode($file); ?>" class="btn-download">⬇️ İndir</a>
                            <a href="filemanager.php?action=delete&file=<?php echo urlencode($file); ?>" class="btn-delete" onclick="return confirm('Silmek istediğinizden emin misiniz?');">🗑️ Sil</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
