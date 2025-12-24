<?php
session_start();
require 'DB.php';

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if (!isset($_GET['profile_id'])) {
    die("Missing profile_id");
}

$db = new DB();
$pdo = $db->getPDO();

$stmt = $pdo->prepare(
    "SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid"
);
$stmt->execute([
    ':pid' => $_GET['profile_id'],
    ':uid' => $_SESSION['user_id']
]);

$profile = $stmt->fetch();
if ($profile === false) {
    die("Access denied");
}

if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid"
    );
    $stmt->execute([
        ':pid' => $_POST['profile_id'],
        ':uid' => $_SESSION['user_id']
    ]);

    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Delete Profile</title>
</head>
<body>
<h1>Confirm Deletion</h1>

<p><?= htmlentities($profile['first_name'].' '.$profile['last_name']) ?></p>

<form method="POST">
<input type="hidden" name="profile_id" value="<?= $profile['profile_id'] ?>">
<input type="submit" name="delete" value="Delete">
<input type="button" value="Cancel" onclick="location.href='index.php'">
</form>
</body>
</html>
