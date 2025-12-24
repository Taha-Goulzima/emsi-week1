<?php
session_start();
require 'DB.php';

$salt = 'XyZzy12*_';

if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = 'Both fields must be filled out';
        header("Location: login.php");
        return;
    }

    $db = new DB();
    $pdo = $db->getPDO();

    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare(
        'SELECT user_id, name FROM users
         WHERE email = :em AND password = :pw'
    );
    $stmt->execute([
        ':em' => $_POST['email'],
        ':pw' => $check
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['name'] = $row['name'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header("Location: login.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Facundo Lubo Login</title>
<link rel="stylesheet"
 href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Please Log In</h1>

<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
    unset($_SESSION['error']);
}
?>

<form method="POST">
    <p>Email <input type="text" name="email" id="email"></p>
    <p>Password <input type="password" name="pass" id="id_1723"></p>
    <input type="submit" onclick="return doValidate();" value="Log In">
    <a href="index.php">Cancel</a>
</form>

<script>
function doValidate() {
    let email = document.getElementById('email').value;
    let pw = document.getElementById('id_1723').value;
    if (email === "" || pw === "") {
        alert("Both fields must be filled out");
        return false;
    }
    if (email.indexOf('@') === -1) {
        alert("Invalid email address");
        return false;
    }
    return true;
}
</script>

</div>
</body>
</html>
