<?php
$x = $_POST['a'] ?? '';

if ($x === 'login') {
    $r = q('SELECT * FROM admins WHERE email=?', [$_POST['email']])->fetch();
    if ($r && password_verify($_POST['pass'], $r['pass'])) {
        $_SESSION['aid'] = $r['id'];
        go('dash');
    }
    $err = 'Wrong login';
} elseif ($a) {
    try {
        if ($x === 's_add') {
            q(
                'INSERT INTO scooters(code,lat,lng,battery) VALUES(?,?,?,?)',
                [strtoupper(trim($_POST['code'])), $_POST['lat'], $_POST['lng'], $_POST['battery']]
            );
        }
        if ($x === 's_save') {
            q(
                'UPDATE scooters SET lat=?,lng=?,battery=?,status=?,reserved_by=NULL,reserved_until=NULL WHERE id=?',
                [$_POST['lat'], $_POST['lng'], $_POST['battery'], $_POST['status'], $_POST['id']]
            );
        }
        if ($x === 's_del') {
            q('DELETE FROM scooters WHERE id=?', [$_POST['id']]);
        }
        if ($x === 'u_del') {
            q('DELETE FROM users WHERE id=?', [$_POST['id']]);
        }
        if ($x === 'reply') {
            q('INSERT INTO messages(uid,admin,body,ts) VALUES(?,1,?,?)', [$_POST['uid'], $_POST['body'], time()]);
        }
        if ($x === 'set') {
            foreach (['unlock', 'per_min', 'min_bat', 'hold_min'] as $k) {
                q('UPDATE settings SET v=? WHERE k=?', [(int)$_POST[$k], $k]);
            }
        }
        if ($x === 'pw') {
            q('UPDATE admins SET pass=? WHERE id=?', [password_hash($_POST['pass'], PASSWORD_DEFAULT), $a]);
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Error: ' . $e->getMessage();
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}