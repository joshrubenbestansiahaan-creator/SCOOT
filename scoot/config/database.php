<?php
include_once 'app.php';

$servername = "localhost";
$database   = "scoot";
$username   = "root";
$password   = "Sinaptel#970";

try {
    $db = new PDO("mysql:host=$servername;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $db->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4");
    $db->exec("USE `$database`");

} catch (PDOException $e) {
    header('Location: ' . BASE_URL . '/config/error.php');
    exit;
}

return $db;