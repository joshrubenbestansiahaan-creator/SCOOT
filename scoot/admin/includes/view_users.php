<table class="dt">
    <tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Rides</th><th></th></tr>
    <?php foreach (q('SELECT u.*,(SELECT COUNT(*) FROM rides WHERE uid=u.id) n FROM users u ORDER BY id DESC')->fetchAll() as $u): ?>
        <tr>
            <td><?=$h($u['name'])?></td>
            <td><?=$h($u['email'])?></td>
            <td><?=$h($u['phone'])?></td>
            <td><?=$u['created_at']?></td>
            <td><?=$u['n']?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="a" value="u_del">
                    <input type="hidden" name="id" value="<?=$u['id']?>">
                    <button class="d" onclick="return confirm('Delete user?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>