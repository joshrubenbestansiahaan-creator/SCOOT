<?php
/**
 * Auto-create database tables & initial default data
 */

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        phone VARCHAR(30),
        pass VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(120) NOT NULL UNIQUE,
        pass VARCHAR(255) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS scooters (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        lat DOUBLE,
        lng DOUBLE,
        battery INT DEFAULT 100,
        status VARCHAR(12) DEFAULT 'available',
        reserved_by INT NULL,
        reserved_until INT NULL,
        FOREIGN KEY(reserved_by) REFERENCES users(id) ON DELETE SET NULL
    )",
    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        provider VARCHAR(30),
        phone VARCHAR(30),
        FOREIGN KEY(uid) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS rides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        sid INT NOT NULL,
        start INT,
        end INT NULL,
        cost INT NULL,
        FOREIGN KEY(uid) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(sid) REFERENCES scooters(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uid INT NOT NULL,
        admin TINYINT DEFAULT 0,
        body TEXT,
        ts INT,
        FOREIGN KEY(uid) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS settings (
        k VARCHAR(30) PRIMARY KEY,
        v VARCHAR(50)
    )"
];

// 1. Jalankan Query pembuatan Tabel
foreach ($tables as $query) {
    $db->exec($query);
}

// 2. Insert Default Settings
$defaultSettings = [
    'unlock'   => 1000,
    'per_min'  => 300,
    'min_bat'  => 20,
    'hold_min' => 10
];

$stmtSetting = $db->prepare('INSERT IGNORE INTO settings(k, v) VALUES(?, ?)');
foreach ($defaultSettings as $key => $val) {
    $stmtSetting->execute([$key, $val]);
}

// 3. Insert Default Admin Account jika belum ada
$hasAdmin = $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if (!$hasAdmin) {
    $db->prepare('INSERT INTO admins(email, pass) VALUES(?, ?)')
       ->execute(['admin@scoot.id', password_hash('admin123', PASSWORD_DEFAULT)]);
}

// 4. Insert Default Scooters Data jika belum ada
$hasScooters = $db->query('SELECT COUNT(*) FROM scooters')->fetchColumn();
if (!$hasScooters) {
    $scootersData = [
        ['8E3677B', -6.3622, 106.8268, 100],
        ['4A91C20', -6.3615, 106.8259, 85],
        ['7F20D3E', -6.3609, 106.8249, 64],
        ['2B77A1C', -6.3603, 106.8241, 12],
        ['9C45E8F', -6.3598, 106.8236, 90]
    ];
    $stmtScooter = $db->prepare('INSERT INTO scooters(code, lat, lng, battery) VALUES(?, ?, ?, ?)');
    foreach ($scootersData as $scooter) {
        $stmtScooter->execute($scooter);
    }
}