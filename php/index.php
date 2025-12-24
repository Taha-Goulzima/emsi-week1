<?php
session_start();
require 'DB.php';

$db = new DB();
$pdo = $db->getPDO();

$stmt = $pdo->query('SELECT profile_id, first_name, last_name, headline, user_id FROM Profile');
$profiles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Facundo Lubo Resume Registry</title>
<link rel="stylesheet"
 href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Facundo Lubo Resume Registry</h1>

<?php
if (isset($_SESSION['success'])) {
    echo '<p style="color:green">'.htmlentities($_SESSION['success']).'</p>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
    unset($_SESSION['error']);
}

if (!isset($_SESSION['user_id'])) {
    echo '<p><a href="login.php">Please log in</a></p>';
} else {
    echo '<p>Welcome '.htmlentities($_SESSION['name']).'</p>';
    echo '<p><a href="add.php">Add New Entry</a> | <a href="logout.php">Logout</a></p>';
}
?>

<table class="table table-bordered">
<tr>
    <th>Name</th>
    <th>Headline</th>
    <th>Action</th>
</tr>

<?php foreach ($profiles as $p): ?>
<tr>
    <td>
        <a href="view.php?profile_id=<?= $p['profile_id'] ?>">
            <?= htmlentities($p['first_name'].' '.$p['last_name']) ?>
        </a>
    </td>
    <td><?= htmlentities($p['headline']) ?></td>
    <td>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $p['user_id']): ?>
            <a href="edit.php?profile_id=<?= $p['profile_id'] ?>">Edit</a> |
            <a href="delete.php?profile_id=<?= $p['profile_id'] ?>">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
