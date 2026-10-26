<aside>
    <img src="../assets/img/logo.png" alt="Logo">
    <?php foreach ($tabs as $k => $v): ?>
        <a href="?p=<?=$k?>" class="<?=$p == $k ? 'on' : ''?>">
            <?=$v?>
            <?php if ($k == 'support'): ?> <em id="bd"></em><?php endif; ?>
        </a>
    <?php endforeach; ?>
    <a href="?p=logout">Log Out</a>
</aside>