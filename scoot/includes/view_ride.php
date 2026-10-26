<?php if (!$ride) go('home'); ?>
<div class="pad c">
    <h2>Your Scoot Is Ready To Use</h2>
    <div class="big" id="t" data-start="<?=$ride['start']?>">00:00</div>
    <div class="box">Cost now: IDR <b id="cost">0</b></div>
    
    <form method="post" onsubmit="return confirm('End ride and lock?')">
        <input type="hidden" name="a" value="end">
        <button class="btn">Park &amp; Lock</button>
    </form>
</div>

<script>
    const U = <?=UNLOCK?>, M = <?=PER_MIN?>;
</script>