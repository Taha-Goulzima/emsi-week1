<?php
session_start();
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

require 'DB.php';
$db = new DB();
$pdo = $db->getPdo();

// Check for profile_id
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$profile_id = $_GET['profile_id'];

// Fetch the profile
$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $profile_id));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    return;
}

// Only the owner can edit
if ($profile['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php");
    return;
}

// Process POST submission
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$profile_id);
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$profile_id);
        return;
    }

    $stmt = $pdo->prepare('UPDATE Profile
        SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sm
        WHERE profile_id = :pid');
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
        ':sm' => $_POST['summary'],
        ':pid' => $profile_id
    ));

    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Edit Profile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
    unset($_SESSION['error']);
}
?>
<form method="POST">
    <p>First Name: <input type="text" name="first_name" value="<?= htmlentities($profile['first_name']) ?>"></p>
    <p>Last Name: <input type="text" name="last_name" value="<?= htmlentities($profile['last_name']) ?>"></p>
    <p>Email: <input type="text" name="email" value="<?= htmlentities($profile['email']) ?>"></p>
    <p>Headline: <input type="text" name="headline" value="<?= htmlentities($profile['headline']) ?>"></p>
    <p>Summary: <input type="text" name="summary" value="<?= htmlentities($profile['summary']) ?>"></p>
    <input type="submit" value="Save">
    <input type="button" value="Cancel" onclick="location.href='index.php'">
</form>
</div>
</body>
</html>
