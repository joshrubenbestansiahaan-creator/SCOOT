<?php if (isset($nav[$p]) || $p === 'ride'): ?>
    <nav>
        <?php foreach ($nav as $k => $v): ?>
            <a href="?p=<?=$k?>" class="<?=$p == $k ? 'on' : ''?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <?=$ic[$k]?>
                </svg>
                <?=$p == $k ? "<span>$v</span>" : ''?>
            </a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>

<script src="assets/vendor/leaflet/leaflet.js"></script>
<script src="assets/app.js"></script>
</body>
</html>