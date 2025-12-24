<?php
session_start();
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

require 'DB.php';
$db = new DB();
$pdo = $db->getPdo();

if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: add.php");
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :hl, :sm)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
        ':sm' => $_POST['summary']
    ));
    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Add New Profile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
    unset($_SESSION['error']);
}
?>
<form method="POST">
    <p>First Name: <input type="text" name="first_name"></p>
    <p>Last Name: <input type="text" name="last_name"></p>
    <p>Email: <input type="text" name="email"></p>
    <p>Headline: <input type="text" name="headline"></p>
    <p>Summary: <input type="text" name="summary"></p>
    <input type="submit" value="Add">
    <input type="button" value="Cancel" onclick="location.href='index.php'">
</form>
</div>
</body>
</html>
