<?php
session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

require './DB.php';
$db = new DB();
$pdo = $db->getPDO();
if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = $_GET['profile_id'];
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :profile_id');
    $stmt->execute(array(':profile_id' => $_GET['profile_id']));
    $profile = $stmt->fetch();
}

if (isset($_SESSION['profile_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :profile_id');
    $stmt->execute(array(':profile_id' => $_SESSION['profile_id']));
    $profile = $stmt->fetch();
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
    && isset($_POST['headline']) && isset($_POST['summary'])) {
        
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['fail'] = 'All values are required';
        header("Location: edit.php?profile_id=" . urlencode($_SESSION['profile_id']));
        return;
    } 
    /* check if email has an '@' */
    if (!strpos($_POST['email'], '@')) {
        $_SESSION['fail_email'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=" . urlencode($_SESSION['profile_id']));
        return;
    }
    
    /* FORM TO UPDATE A PROFILE */
   
    else {
        $stmt = $pdo->prepare('UPDATE Profile
            SET first_name = :first_name,
                last_name = :last_name,
                email = :email,
                headline = :headline,
                summary = :summary
            WHERE profile_id = :profile_id
        ');
        $stmt->execute(array(
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary'],
                ':profile_id' => $_GET['profile_id']
        ));
        $_SESSION['success'] = $profile['first_name'] . " updated";
        header("Location: index.php");
        return;
    }

}

else $_SESSION['fail']="All fields are required";

?>

<!DOCTYPE html>
<html>
<head>
    <?php
        require './bootstrap.php';
    ?>
<title> Facundo Lubo's Edit registry</title>
</head>
<body>
    <div class="container">
        <h1> Edit the profile of <?php echo htmlentities($profile['first_name']); ?> </h1>
        <?php
        if (isset($_SESSION['fail'])) {
            echo('<p style="color: red;">' . htmlentities($_SESSION['fail']) . "</p>");
            unset($_SESSION['fail']);
        }
        if (isset($_SESSION['fail_email'])) {
            echo('<p style="color: red;">' . htmlentities($_SESSION['fail_email']) . "</p>");
            unset($_SESSION['fail']);
        }
        if (isset($_SESSION['success'])) {
            echo('<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>");
            unset($_SESSION['success']);
        }
        ?>
    <form method="POST">
        <p>First Name: <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : $profile['first_name']; ?>"/><br/></p>
        <p>Last Name: <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : $profile['last_name']; ?>"/><br/></p>
        <p>Email: <input type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : $profile['email']; ?>"/><br/></p>
        <p>Headline: <input type="text" name="headline" value="<?php echo isset($_POST['headline']) ? $_POST['headline'] : $profile['headline']; ?>"/><br/></p>
        <p>Summary: <input type="text" name="summary" value="<?php echo isset($_POST['summary']) ? $_POST['summary'] : $profile['summary']; ?>"/><br/></p>
        <input type="hidden" name="profile_id" value="<?php echo isset($_GET['profile_id']) ? $_GET['profile_id'] : (isset($profile['profile_id']) ? $profile['profile_id'] : ''); ?>">
        <input type="submit" value="Save">
        <input type="button" value="Cancel" onclick="location.href='index.php'">
    </form>
    </div>
</body>
</html>