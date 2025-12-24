<?php
session_start();
require 'DB.php';

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$db = new DB();
$pdo = $db->getPdo();

$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Profile View</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Profile Information</h1>
<p><b>First Name:</b> <?= htmlentities($profile['first_name']) ?></p>
<p><b>Last Name:</b> <?= htmlentities($profile['last_name']) ?></p>
<p><b>Email:</b> <?= htmlentities($profile['email']) ?></p>
<p><b>Headline:</b> <?= htmlentities($profile['headline']) ?></p>
<p><b>Summary:</b> <?= htmlentities($profile['summary']) ?></p>

<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profile['user_id']): ?>
<p>
    <a href="edit.php?profile_id=<?= $profile['profile_id'] ?>">Edit</a> |
    <a href="delete.php?profile_id=<?= $profile['profile_id'] ?>">Delete</a>
</p>
<?php endif; ?>

<p><a href="index.php">Back to List</a></p>
</div>
</body>
</html>
