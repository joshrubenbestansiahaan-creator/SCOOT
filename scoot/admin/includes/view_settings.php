<?php 
$S = settings(); 
$settingLabels = [
    'unlock'   => 'Unlock fee (IDR)',
    'per_min'  => 'Price per minute (IDR)',
    'min_bat'  => 'Min battery to unlock (%)',
    'hold_min' => 'Reservation hold (minutes)'
];
?>

<!-- Form Pengaturan Biaya & Parameter -->
<form method="post" class="r" style="display:grid; max-width:360px; gap:10px">
    <input type="hidden" name="a" value="set">
    
    <?php foreach ($settingLabels as $key => $label): ?>
        <label>
            <?=$label?><br>
            <input name="<?=$key?>" type="number" value="<?=$h($S[$key] ?? '')?>" style="width:100%">
        </label>
    <?php endforeach; ?>
    
    <button>Save</button>
</form>

<!-- Form Ganti Password Admin -->
<form method="post" class="r" style="margin-top:20px; max-width:360px">
    <input type="hidden" name="a" value="pw">
    <input name="pass" type="password" placeholder="New admin password" minlength="6" required>
    <button>Change Password</button>
</form>