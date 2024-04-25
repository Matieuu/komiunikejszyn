<?php

session_start();

if (isset($_SESSION['logged']) && $_SESSION['logged']) {
    session_unset();
    $_SESSION['logged'] = true;
    header('Location: ../home/');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Matieuu" />
    <title>Komiunikejszyn</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <form action="login.php" method="post">
        <input type="text" name="login" id="login" placeholder="username" <?= isset($_SESSION['login']) ? 'value="'.$_SESSION['login'].'"' : '' ?> />
        <input type="password" name="pass" id="pass" placeholder="password" />
        <input type="submit" value="Log In" />
        <a href="../signup/">Sign Up</a>
        <?= isset($_SESSION['err']) ? '<p class="err">'.$_SESSION['err'].'</p>' : '' ?>
    </form>
</body>
</html>