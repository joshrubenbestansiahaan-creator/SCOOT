<?php 
$uid = (int)($_GET['u'] ?? 0); 
$last = 0;
?>

<div class="split">
    <!-- List Percakapan User -->
    <div>
        <?php foreach (q('SELECT u.id, u.name, m.body, m.admin FROM users u JOIN messages m ON m.id=(SELECT MAX(id) FROM messages WHERE uid=u.id) ORDER BY m.id DESC')->fetchAll() as $c): ?>
            <a class="card conv <?=$uid == $c['id'] ? 'sel' : ''?>" href="?p=support&u=<?=$c['id']?>">
                <b>
                    <?=$h($c['name'])?>
                    <?=$c['admin'] ? '' : ' <i class="dot"></i>'?>
                </b>
                <span><?=$h(mb_strimwidth($c['body'], 0, 40, '...'))?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Isi Chat Thread -->
    <div>
        <?php if ($uid): ?>
            <div id="thread">
                <?php foreach (q('SELECT * FROM messages WHERE uid=? ORDER BY id', [$uid])->fetchAll() as $m): ?>
                    <?php $last = $m['id']; ?>
                    <div class="msg <?=$m['admin'] ? 'a' : ''?>" data-id="<?=$m['id']?>">
                        <?=$h($m['body'])?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Form Balas Chat -->
            <form method="post" class="r">
                <input type="hidden" name="a" value="reply">
                <input type="hidden" name="uid" value="<?=$uid?>">
                <input name="body" placeholder="Reply..." style="flex:1" required autofocus>
                <button>Send</button>
            </form>

            <script>
                window.THREAD = { u: <?=$uid?>, last: <?=$last?> };
            </script>
        <?php else: ?>
            <p>Pick a conversation.</p>
        <?php endif; ?>
    </div>
</div>