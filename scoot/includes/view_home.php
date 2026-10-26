<?php 
$res = $q('SELECT * FROM scooters WHERE reserved_by=?', [$u])->fetch();
if ($res): 
?>
    <div class="sheet res">
        <div>
            <small>Reserved · <span id="hold" data-left="<?=max(0, $res['reserved_until'] - time())?>"></span></small>
            <b><?=$res['code']?></b>
        </div>
        <a class="btn sm" href="?p=scan&code=<?=$res['code']?>">Scan</a>
        <form method="post">
            <input type="hidden" name="a" value="cancel">
            <button class="btn sm x">Cancel</button>
        </form>
    </div>
<?php endif; ?>

<div id="map"></div>
<div id="card" class="sheet" hidden></div>

<script>
    const SCOOTERS = <?=json_encode($db->query("SELECT code,lat,lng,battery FROM scooters WHERE status='available'")->fetchAll(PDO::FETCH_ASSOC))?>;
</script>