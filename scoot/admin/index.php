<?php
require '../db.php';

$p = $_GET['p'] ?? 'dash';
$a = $_SESSION['aid'] ?? null;
$err = '';

$h = fn($s) => htmlspecialchars((string)$s);

// Handlers / Routing Khusus
if ($p === 'logout') {
    unset($_SESSION['aid']);
    go('dash');
}

$UNQL = "SELECT u.id, u.name, m.body FROM users u JOIN messages m ON m.id=(SELECT MAX(id) FROM messages WHERE uid=u.id) WHERE m.admin=0 ORDER BY m.id DESC";
$UNQ  = "SELECT COUNT(*) FROM users u JOIN messages m ON m.id=(SELECT MAX(id) FROM messages WHERE uid=u.id) WHERE m.admin=0";

if ($a && $p === 'unread') {
    echo q($UNQ)->fetchColumn();
    exit;
}

if ($a && $p === 'poll') {
    header('Content-Type: application/json');
    $u = (int)($_GET['u'] ?? 0);
    $last = (int)($_GET['last'] ?? 0);
    echo json_encode(q('SELECT id, admin, body FROM messages WHERE uid=? AND id>? ORDER BY id', [$u, $last])->fetchAll());
    exit;
}

// Proses Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'includes/actions.php';
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$tabs = [
    'dash'     => 'Dashboard',
    'scooters' => 'Scooters',
    'users'    => 'Users',
    'rides'    => 'Rides',
    'support'  => 'Support',
    'settings' => 'Settings'
];

include 'includes/header.php';

if (!$a): ?>
    <form method="post" class="login">
        <img src="../assets/img/logo.png" width="70" style="margin:auto">
        <h2>SCOOT Admin</h2>
        <?php if ($err): ?><div class="err"><?=$err?></div><?php endif; ?>
        <input type="hidden" name="a" value="login">
        <input name="email" type="email" placeholder="Email" required>
        <input name="pass" type="password" placeholder="Password" required>
        <button>Sign In</button>
    </form>
<?php else:
    include 'includes/sidebar.php';
    ?>
    <section>
        <h1><?=$tabs[$p] ?? ''?></h1>
        <?php if ($flash): ?><div class="err"><?=$h($flash)?></div><?php endif; ?>

        <?php
        switch ($p) {
            case 'dash':
                include 'includes/view_dash.php';
                break;
            case 'scooters':
                include 'includes/view_scooters.php';
                break;
            case 'users':
                include 'includes/view_users.php';
                break;
            case 'rides':
                include 'includes/view_rides.php';
                break;
            case 'support':
                include 'includes/view_support.php';
                break;
            case 'settings':
                include 'includes/view_settings.php';
                break;
        }

        include 'includes/footer.php';
        ?>
    </section>
<?php endif; ?>