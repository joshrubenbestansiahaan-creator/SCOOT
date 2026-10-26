<?php $su = ($p === 'signup'); ?>
<div class="pad">
    <img class="logo sm" src="assets/img/logo.png" alt="SCOOT">
    <h2><?=$su ? 'Create Account' : 'Welcome to SCOOT'?></h2>
    <p class="mut">Freedom. Speed. Style.</p>

    <?php if ($err): ?>
        <div class="err"><?=$err?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="a" value="<?=$p?>">
        
        <?php if ($su): ?>
            <label>Full Name</label>
            <input name="name" required>
            <label>Phone Number</label>
            <input name="phone" required>
        <?php endif; ?>

        <label>Email</label>
        <input type="email" name="email" required>
        
        <label>Password</label>
        <input type="password" name="pass" minlength="6" required>
        
        <button class="btn">Sign <?=$su ? 'Up' : 'In'?></button>
    </form>

    <p class="mut c">
        <?=$su 
            ? 'Already have an account? <a href="?p=signin">Sign in</a>' 
            : 'Don\'t have an account? <a href="?p=signup">Sign up</a>'
        ?>
    </p>
</div>