<?php
$a = $_POST['a'] ?? '';

// Sign Up
if ($a === 'signup') {
    try {
        $q(
            'INSERT INTO users(name,email,phone,pass) VALUES(?,?,?,?)',
            [$_POST['name'], $_POST['email'], $_POST['phone'], password_hash($_POST['pass'], PASSWORD_DEFAULT)]
        );
        $_SESSION['uid'] = $db->lastInsertId();
        go('home');
    } catch (Exception $e) {
        $err = 'Email already used';
        $p = 'signup';
    }
}

// Sign In
if ($a === 'signin') {
    $r = $q('SELECT * FROM users WHERE email=?', [$_POST['email']])->fetch(PDO::FETCH_ASSOC);
    if ($r && password_verify($_POST['pass'], $r['pass'])) {
        $_SESSION['uid'] = $r['id'];
        go('home');
    }
    $err = 'Wrong email or password';
    $p = 'signin';
}

// Pembayaran: Hapus Method
if ($a === 'paydel') {
    $q('DELETE FROM payments WHERE id=? AND uid=?', [$_POST['id'], $u]);
    go('payment');
}

// Pembayaran: Tambah Method
if ($a === 'pay') {
    $q('INSERT INTO payments(uid,provider,phone) VALUES(?,?,?)', [$u, $_POST['provider'], $_POST['phone']]);
    go('payment');
}

// Unlock Skuter
if ($a === 'unlock') {
    $has = $q('SELECT COUNT(*) FROM payments WHERE uid=?', [$u])->fetchColumn();
    $s   = $q('SELECT * FROM scooters WHERE code=?', [strtoupper(trim($_POST['code']))])->fetch(PDO::FETCH_ASSOC);

    if (!$has) {
        $err = 'Add payment method first';
    } elseif (!$s) {
        $err = 'Scoot code not found';
    } elseif ($s['status'] == 'maintenance') {
        $err = 'Scoot under maintenance';
    } elseif ($s['status'] == 'used' || ($s['status'] == 'reserved' && $s['reserved_by'] != $u)) {
        $err = 'Scoot is in use';
    } elseif ($s['battery'] < MIN_BAT) {
        $err = 'Battery too low';
    } else {
        $q('INSERT INTO rides(uid,sid,start) VALUES(?,?,?)', [$u, $s['id'], time()]);
        $q("UPDATE scooters SET status='used',reserved_by=NULL WHERE id=?", [$s['id']]);
        go('ride');
    }
    $p = 'scan';
}

// Reservasi Skuter
if ($a === 'reserve') {
    if (!$q('SELECT COUNT(*) FROM scooters WHERE reserved_by=?', [$u])->fetchColumn()) {
        $q("UPDATE scooters SET status='reserved',reserved_by=?,reserved_until=? WHERE code=? AND status='available' AND battery>=?", 
            [$u, time() + 60 * HOLD, $_POST['code'], MIN_BAT]
        );
    }
    go('home');
}

// Batal Reservasi
if ($a === 'cancel') {
    $q("UPDATE scooters SET status='available',reserved_by=NULL,reserved_until=NULL WHERE reserved_by=?", [$u]);
    go('home');
}

// Selesai Ride
if ($a === 'end') {
    $r = $q('SELECT * FROM rides WHERE uid=? AND end IS NULL', [$u])->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $m = max(1, ceil((time() - $r['start']) / 60));
        $c = UNLOCK + PER_MIN * $m;
        $q('UPDATE rides SET end=?,cost=? WHERE id=?', [time(), $c, $r['id']]);
        $q("UPDATE scooters SET status='available' WHERE id=?", [$r['sid']]);
        $q('INSERT INTO messages(uid,admin,body,ts) VALUES(?,1,?,?)', [
            $u,
            "Ride done. $m min. Total IDR " . number_format($c),
            time()
        ]);
    }
    go('inbox');
}

// Kirim Pesan / Chat
if ($a === 'msg') {
    $first = !$q('SELECT COUNT(*) FROM messages WHERE uid=? AND admin=0', [$u])->fetchColumn();
    $q('INSERT INTO messages(uid,admin,body,ts) VALUES(?,0,?,?)', [$u, trim($_POST['body']), time()]);
    if ($first) {
        $q('INSERT INTO messages(uid,admin,body,ts) VALUES(?,1,?,?)', [$u, 'Thanks! Scootmin will reply soon.', time()]);
    }
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo 'ok';
        exit;
    }
    go('inbox');
}