<?php 
$c = fn($sql) => q($sql)->fetchColumn(); 
$unqQuery = "SELECT COUNT(*) FROM users u JOIN messages m ON m.id=(SELECT MAX(id) FROM messages WHERE uid=u.id) WHERE m.admin=0";
$unqlQuery = "SELECT u.id, u.name, m.body FROM users u JOIN messages m ON m.id=(SELECT MAX(id) FROM messages WHERE uid=u.id) WHERE m.admin=0 ORDER BY m.id DESC";
?>

<div class="cards">
    <div class="card"><b><?=$c('SELECT COUNT(*) FROM users')?></b><span>Users</span></div>
    <div class="card"><b><?=$c('SELECT COUNT(*) FROM scooters')?></b><span>Scooters</span></div>
    <div class="card"><b><?=$c("SELECT COUNT(*) FROM scooters WHERE status='available'")?></b><span>Available</span></div>
    <div class="card"><b><?=$c('SELECT COUNT(*) FROM rides WHERE end IS NULL')?></b><span>Active rides</span></div>
    <div class="card"><b><?=$c('SELECT COUNT(*) FROM rides WHERE end IS NOT NULL')?></b><span>Total rides</span></div>
    <div class="card"><b>IDR <?=number_format((int)$c('SELECT COALESCE(SUM(cost),0) FROM rides'))?></b><span>Revenue</span></div>
    <div class="card"><b><?=$c('SELECT COUNT(*) FROM scooters WHERE battery<20')?></b><span>Low battery</span></div>
    <div class="card"><b><?=$c($unqQuery)?></b><span>Unanswered chats</span></div>
</div>

<div class="grid2">
    <div>
        <h3>Recent rides</h3>
        <table class="dt">
            <tr><th>#</th><th>User</th><th>Scoot</th><th>Start</th><th>Cost</th></tr>
            <?php foreach (q('SELECT r.id,u.name,s.code,r.start,r.cost FROM rides r LEFT JOIN users u ON u.id=r.uid LEFT JOIN scooters s ON s.id=r.sid ORDER BY r.id DESC LIMIT 100')->fetchAll() as $r): ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td><?=$h($r['name'])?></td>
                    <td><?=$h($r['code'])?></td>
                    <td data-s="<?=$r['start']?>"><?=date('d M H:i', $r['start'])?></td>
                    <td><?=$r['cost'] !== null ? 'IDR ' . number_format($r['cost']) : 'riding'?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div>
        <h3>Fleet status</h3>
        <table class="dt">
            <tr><th>Code</th><th>Battery</th><th>Status</th></tr>
            <?php foreach (q('SELECT code,battery,status FROM scooters ORDER BY battery')->fetchAll() as $s): ?>
                <tr><td><?=$h($s['code'])?></td><td><?=$s['battery']?>%</td><td><?=$h($s['status'])?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div>
        <h3>Latest users</h3>
        <table class="dt">
            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
            <?php foreach (q('SELECT name,email,created_at FROM users ORDER BY id DESC LIMIT 100')->fetchAll() as $u): ?>
                <tr><td><?=$h($u['name'])?></td><td><?=$h($u['email'])?></td><td><?=$u['created_at']?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div>
        <h3>Unanswered chats</h3>
        <table class="dt">
            <tr><th>User</th><th>Last message</th><th></th></tr>
            <?php foreach (q($unqlQuery)->fetchAll() as $c): ?>
                <tr><td><?=$h($c['name'])?></td><td><?=$h($c['body'])?></td><td><a href="?p=support&u=<?=$c['id']?>">Reply</a></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>