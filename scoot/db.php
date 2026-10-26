<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = require 'config/database.php';
require 'config/schema.php';

function q($sql, $params = []) {
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function settings() {
    global $db;
    $res = [];
    foreach ($db->query("SELECT k, v FROM settings")->fetchAll() as $row) {
        $res[$row['k']] = $row['v'];
    }
    return $res;
}

function go($p) {
    header("Location: ?p=$p");
    exit;
}