<?php
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

require_once 'DB.php';
$db = new DB();
$pdo = $db->getPDO();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validation: all fields required
    if (
        strlen($_POST['first_name']) < 1 ||
        strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 ||
        strlen($_POST['summary']) < 1
    ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }

    // Validation: email must contain @
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: add.php");
        return;
    }

    // Insert profile
    $stmt = $pdo->prepare(
        'INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :he, :su)'
    );

    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn'  => $_POST['first_name'],
        ':ln'  => $_POST['last_name'],
        ':em'  => $_POST['email'],
        ':he'  => $_POST['headline'],
        ':su'  => $_POST['summary']
    ]);

    $_SESSION['success'] = 'Profile added';
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Facundo Lubo's Add Profile</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
    <h1>Add New Profile</h1>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <form method="POST">
        <p>First Name:
            <input type="text" name="first_name">
        </p>
        <p>Last Name:
            <input type="text" name="last_name">
        </p>
        <p>Email:
            <input type="text" name="email">
        </p>
        <p>Headline:
            <input type="text" name="headline">
        </p>
        <p>Summary:
            <textarea name="summary"></textarea>
        </p>
        <input type="submit" value="Add">
        <a href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
