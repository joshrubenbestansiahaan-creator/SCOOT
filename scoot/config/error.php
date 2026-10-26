<?php require_once __DIR__ . '/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOOT - Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            text-align: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #ffffff;
            color: #111111;
        }
        img { width: 100px; display: block; margin: 0 auto 16px; }
        p { font-weight: 600; font-size: 16px; margin: 0; }
    </style>
</head>
<body>
    <div>
        <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="SCOOT Logo">
        <p>SISTEM SEDANG PEMELIHARAAN</p>
    </div>
</body>
</html>