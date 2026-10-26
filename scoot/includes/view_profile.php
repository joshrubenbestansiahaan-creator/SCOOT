<?php 
$s = $_GET['s'] ?? '';
$me = $q('SELECT name,email FROM users WHERE id=?', [$u])->fetch(PDO::FETCH_ASSOC);
?>

<div class="pad">
    <?php if (!$s): ?>
        <h2><?=htmlspecialchars($me['name'])?></h2>
        <p class="mut"><?=htmlspecialchars($me['email'])?></p>

        <?php 
        $menuItems = [
            ['?p=profile&s=history', 'Riding History', 'g', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
            ['?p=profile&s=guide', 'Riding Guide', 'y', '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M8 7h7"/>'],
            ['?p=profile&s=biz', 'Scoot For Business', 'p', '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>'],
            ['?p=profile&s=settings', 'Settings', 'b', '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>'],
            ['?p=logout', 'Log Out', 'o', '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>']
        ];
        foreach ($menuItems as $m): 
        ?>
            <a class="item mi <?=$m[2]?>" href="<?=$m[0]?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?=$m[3]?></svg>
                <span><?=$m[1]?></span>
                <svg class="chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
            </a>
        <?php endforeach; ?>

    <?php else: ?>
        <a href="?p=profile">← Back</a>

        <?php if ($s === 'history'): 
            $rs = $q('SELECT * FROM rides WHERE uid=? AND end IS NOT NULL ORDER BY id DESC', [$u])->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h2>Riding History</h2>
            <?php if (!$rs): ?>
                <div class="box">Oops... You haven't used Scoot</div>
            <?php endif; ?>
            
            <?php foreach ($rs as $r): ?>
                <div class="item">
                    <b><?=date('d M H:i', $r['start'])?></b>
                    <span>IDR <?=number_format($r['cost'])?></span>
                </div>
            <?php endforeach; ?>

        <?php elseif ($s === 'guide'): ?>
            <h2>Riding Guide</h2>
            <ol>
                <li>Click the scan on the navigation bar</li>
                <li>Scan the QR code on the Scoot</li>
                <li>It's all set!</li>
            </ol>

        <?php elseif ($s === 'settings'): ?>
            <h2>Settings</h2>
            <p class="mut">Appearance</p>
            <div class="themes">
                <?php 
                $themes = [
                    ['light', 'Light', '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>'],
                    ['dark', 'Dark', '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>'],
                    ['night', 'Night', '<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z"/><path d="M17 3v4M15 5h4"/>'],
                    ['auto', 'Auto', '<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z"/>']
                ];
                foreach ($themes as $t): 
                ?>
                    <button type="button" data-t="<?=$t[0]?>">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?=$t[2]?></svg>
                        <span><?=$t[1]?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="box">
                <b>Dark</b>: dark screen.<br>
                <b>Night</b>: warm and dim, less blue light for night rides.<br>
                <b>Auto</b>: follow your phone.
            </div>

        <?php else: ?>
            <h2>Scoot For Business</h2>
            <p>Business plan: IDR 1,000,000 for 10 member · IDR 2,000,000 for 25 member · IDR 3,000,000 for 50 member.</p>
        <?php endif; ?>

    <?php endif; ?>
</div>