<?php $ms = $q('SELECT * FROM messages WHERE uid=? ORDER BY ts,id', [$u])->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="pad chat">
    <!-- Header Sticky (Tetap di atas) -->
    <div class="chat-head">
        <h2>Scootmin <small>Online</small></h2>
    </div>
    
    <!-- Area Chat (Satu-satunya area yang bisa di-scroll) -->
    <div class="msgs" id="msgs" data-last="<?=$ms ? end($ms)['id'] : 0?>">
        <?php if (!$ms): ?>
            <div class="m a">Yoo! May I help you?</div>
        <?php endif; ?>
        
        <?php foreach ($ms as $m): ?>
            <div class="m <?=$m['admin'] ? 'a' : 'u'?>" data-id="<?=$m['id']?>">
                <?=htmlspecialchars($m['body'])?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Form Input (Tetap terkunci di bawah) -->
    <form method="post" class="row" id="chatf">
        <input type="hidden" name="a" value="msg">
        <input name="body" placeholder="Message" required autocomplete="off">
        <button class="btn sm">Send</button>
    </form>
</div>

<script>
    // Otomatis scroll ke pesan paling bawah
    const msgsContainer = document.getElementById('msgs');
    if (msgsContainer) {
        msgsContainer.scrollTop = msgsContainer.scrollHeight;
    }
</script>