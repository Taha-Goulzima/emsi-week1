<?php
session_start();

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (isset($_POST['email']) && isset($_POST['pass'])) { 
    require 'DB.php';
    $salt = 'XyZzy12*_';
    $db = new DB();
    $pdo = $db->getPdo();

    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: login.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Facundo Lubo Login Page</title>
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
    <label for="email">Email</label>
    <input type="text" name="email" id="email"><br/>
    <label for="id_1723">Password</label>
    <input type="password" name="pass" id="id_1723"><br/>
    <input type="submit" value="Log In">
    <input type="submit" name="cancel" value="Cancel">
</form>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr=" + addr + " pw=" + pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if (addr.indexOf('@') == -1) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
}
</script>

</div>
</body>
</html>
