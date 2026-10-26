<?php
require 'db.php';

$S = settings();
define('UNLOCK', (int)$S['unlock']);
define('PER_MIN', (int)$S['per_min']);
define('MIN_BAT', (int)$S['min_bat']);
define('HOLD', (int)$S['hold_min']);

$p = $_GET['p'] ?? 'splash';
$u = $_SESSION['uid'] ?? null;
$err = '';

if (!$u && !in_array($p, ['splash', 'welcome', 'signin', 'signup'])) {
    go('signin');
}

$q = fn($sql, $a = []) => ($s = $db->prepare($sql)) && $s->execute($a) ? $s : null;

$q("UPDATE scooters SET status='available', reserved_by=NULL, reserved_until=NULL WHERE status='reserved' AND reserved_until < ?", [time()]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'includes/actions.php';
}

if ($p === 'logout') {
    session_destroy();
    go('signin');
}

if ($p === 'poll') {
    header('Content-Type: application/json');
    $lastId = (int)($_GET['last'] ?? 0);
    echo json_encode($q('SELECT id, admin, body FROM messages WHERE uid=? AND id>? ORDER BY id', [$u, $lastId])->fetchAll());
    exit;
}

$ride = $u ? $q('SELECT * FROM rides WHERE uid=? AND end IS NULL', [$u])->fetch(PDO::FETCH_ASSOC) : null;
if ($ride && in_array($p, ['home', 'scan'])) {
    go('ride');
}

$ic = [
    'home'    => '<path d="M3 11l9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
    'payment' => '<rect x="3" y="6" width="18" height="14" rx="3"/><path d="M3 10h18M16 15h2"/>',
    'scan'    => '<path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3M7 12h10"/>',
    'inbox'   => '<path d="M4 5h16v11H9l-5 4z"/>',
    'profile' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/>'
];
$nav = [
    'home'    => 'Home',
    'payment' => 'Payment',
    'scan'    => 'Scan',
    'inbox'   => 'Inbox',
    'profile' => 'Profile'
];

include 'includes/header.php';
?>

<main class="phone <?=$p?>">
    <?php
    switch ($p) {
        case 'splash':
            include 'includes/view_splash.php';
            break;
        case 'welcome':
            include 'includes/view_welcome.php';
            break;
        case 'signin':
        case 'signup':
            include 'includes/view_signin.php';
            break;
        case 'home':
            include 'includes/view_home.php';
            break;
        case 'payment':
            include 'includes/view_payment.php';
            break;
        case 'scan':
            include 'includes/view_scan.php';
            break;
        case 'ride':
            include 'includes/view_ride.php';
            break;
        case 'inbox':
            include 'includes/view_inbox.php';
            break;
        case 'profile':
            include 'includes/view_profile.php';
            break;
    }
    ?>
</main>

<?php include 'includes/footer.php'; ?>