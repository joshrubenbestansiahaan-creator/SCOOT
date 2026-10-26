<table class="dt">
    <tr>
        <th>#</th>
        <th>User</th>
        <th>Scoot</th>
        <th>Start</th>
        <th>Min</th>
        <th>Cost</th>
    </tr>
    <?php foreach (q('SELECT r.*, u.name, s.code FROM rides r LEFT JOIN users u ON u.id=r.uid LEFT JOIN scooters s ON s.id=r.sid ORDER BY r.id DESC LIMIT 200')->fetchAll() as $r): ?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=$h($r['name'])?></td>
            <td><?=$h($r['code'])?></td>
            <td data-s="<?=$r['start']?>"><?=date('d M Y H:i', $r['start'])?></td>
            <td><?=$r['end'] ? ceil(($r['end'] - $r['start']) / 60) : '<i>riding</i>'?></td>
            <td><?=$r['cost'] !== null ? 'IDR ' . number_format($r['cost']) : '-'?></td>
        </tr>
    <?php endforeach; ?>
</table>