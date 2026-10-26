<?php 
$S = settings();
$min = (int)$S['min_bat'];
$own = [];
foreach (q('SELECT r.sid,u.name,r.start FROM rides r JOIN users u ON u.id=r.uid WHERE r.end IS NULL')->fetchAll() as $x) {
    $own[$x['sid']] = $x;
}
?>

<form method="post" class="r">
    <input type="hidden" name="a" value="s_add">
    <b>New scooter</b>
    <input name="code" placeholder="Code (from QR)" title="Unique ID printed in the QR" style="width:150px" required>
    <input name="lat" placeholder="Latitude e.g. -6.3622" title="North-south position" style="width:170px" required>
    <input name="lng" placeholder="Longitude e.g. 106.8268" title="East-west position" style="width:180px" required>
    <input name="battery" type="number" value="100" min="0" max="100" title="Battery percent" style="width:80px">
    <button>Add</button>
</form>

<div class="r hd">
    <span style="width:90px">Code</span>
    <span style="width:120px">Latitude</span>
    <span style="width:120px">Longitude</span>
    <span style="width:70px">Battery %</span>
    <span style="width:130px">Status</span>
    <span>Actions</span>
</div>

<?php foreach (q('SELECT s.*,u.name rname FROM scooters s LEFT JOIN users u ON u.id=s.reserved_by ORDER BY s.id')->fetchAll() as $s):
    if ($s['status'] == 'used') {
        $c = 'use';
        $o = $own[$s['id']] ?? null;
        $note = 'In use' . ($o ? ' by ' . $o['name'] . ' since ' . date('H:i', $o['start']) : '') . '. Not shown on the map until the ride ends.';
    } elseif ($s['status'] == 'reserved') {
        $c = 'use';
        $note = 'Reserved' . ($s['rname'] ? ' by ' . $s['rname'] : '') . ($s['reserved_until'] ? ' until ' . date('H:i', $s['reserved_until']) : '') . '. Released automatically when time is up.';
    } elseif ($s['status'] == 'maintenance') {
        $c = 'mnt';
        $note = 'Under maintenance. Hidden from the customer map, cannot be unlocked.';
    } elseif ($s['battery'] < $min) {
        $c = 'low';
        $note = 'Low battery (below ' . $min . '%). Shown on the map, but customers cannot book or unlock it. Charge it, then update the battery.';
    } else {
        $c = 'ok';
        $note = 'Ready. Shown on the customer map, can be booked and unlocked.';
    }
?>
    <form method="post" class="r">
        <input type="hidden" name="id" value="<?=$s['id']?>">
        <b style="width:90px"><?=$h($s['code'])?></b>
        <input name="lat" value="<?=$s['lat']?>" title="Latitude">
        <input name="lng" value="<?=$s['lng']?>" title="Longitude">
        <input name="battery" type="number" value="<?=$s['battery']?>" min="0" max="100" style="width:70px" title="Battery percent">
        <select name="status" style="width:130px" title="Scooter status">
            <?php foreach (['available', 'reserved', 'used', 'maintenance'] as $o): ?>
                <option <?=$s['status'] == $o ? 'selected' : ''?>><?=$o?></option>
            <?php endforeach; ?>
        </select>
        <button name="a" value="s_save" title="Save this row">Save</button>
        <button name="a" value="s_del" class="d" title="Delete scooter" onclick="return confirm('Delete scooter and its ride history?')">Delete</button>
        <p class="note <?=$c?>"><?=$h($note)?></p>
    </form>
<?php endforeach; ?>