<?php $ps = $q('SELECT * FROM payments WHERE uid=?', [$u])->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="pad">
    <h2>Payment</h2>
    
    <?php foreach ($ps as $x): ?>
        <div class="item pm">
            <div>
                <b><?=htmlspecialchars($x['provider'])?></b><br>
                <span class="mut"><?=htmlspecialchars($x['phone'])?></span>
            </div>
            <form method="post" onsubmit="return confirm('Remove this payment method?')">
                <input type="hidden" name="a" value="paydel">
                <input type="hidden" name="id" value="<?=$x['id']?>">
                <button class="del" aria-label="Remove">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>
                    </svg>
                </button>
            </form>
        </div>
    <?php endforeach; ?>

    <?php if (!$ps): ?>
        <div class="box">Add Your Payment Method</div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="a" value="pay">
        <label>Choose Payment</label>
        <select name="provider">
            <option>ShopeePay</option>
            <option>Dana</option>
            <option>GoPay</option>
        </select>
        
        <label>Phone Number</label>
        <input name="phone" placeholder="+62..." required>
        
        <button class="btn">Add Payment</button>
    </form>
</div>