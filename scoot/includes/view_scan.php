<div class="pad dark">
    <div class="frame">
        <video id="cam" playsinline muted></video>
    </div>
    <p class="c" id="st">Make sure the QR is not blur and have a good light.</p>
    
    <script src="assets/vendor/jsQR.js"></script>

    <?php if ($err): ?>
        <div class="err"><?=$err?></div>
    <?php endif; ?>

    <form method="post" id="f">
        <input type="hidden" name="a" value="unlock">
        <input name="code" value="<?=htmlspecialchars($_GET['code'] ?? '')?>" placeholder="Scoot code (QR)" required>
        <div class="box">Unlock IDR <?=number_format(UNLOCK)?> · IDR <?=PER_MIN?>/min</div>
        <button class="btn">Unlock</button>
    </form>
</div>