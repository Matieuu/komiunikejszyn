<?php

session_start();

if (!isset($_SESSION['logged']) || !isset($_SESSION['ID']) || !$_SESSION['logged'] || !isset($_GET['id'])) {
    session_unset();
    header('Location: ../login/');
    exit;
}

if ($_SESSION['ID'] != $_GET['id']) {
    header('Location: ./index.php?id='.$_SESSION['ID'].'&mess='.$_GET['mess'].'&count='.$_GET['count']);
    exit;
}

$con = require_once '../backend/connect.php';
require_once '../backend/utils.php';

if (isset($_POST['text']) && strlen($_POST['text']) > 0 && strlen($_POST['text']) < 255) {
    $stmt = $con->prepare("INSERT INTO mess(sender, chat, content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $_GET['id'], $_GET['mess'], $_POST['text']);
    $stmt->execute();
    $count = $_GET['count'] + 1;
    header('Location: ./index.php?id='.$_GET['id'].'&mess='.$_GET['mess'].'&count='.$count);
    exit;
} elseif (isset($_POST['text']) && (strlen($_POST['text']) < 0 || strlen($_POST['text']) > 255)) {
    $_SESSION['alert'] = alert("Tekst musi mieć długość pomiędzy 0 a 255 znaków");
    header('Location: ./index.php?id='.$_GET['id'].'&mess='.$_GET['mess'].'&count='.$_GET['count']);
    exit;
}

if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}

$id = '%:'.$_GET['id'].':%';
if (!isset($_GET['mess']))
    $chats = get_chats($con, $id);
else $mess = get_mess($con, $_GET['mess'], $_GET['count']);
// echo '<pre>';
// print_r($chats);
// echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Matieuu" />
    <title>Komiunikejszyn</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
        if (!isset($_GET['mess'])) {
            echo '<section>';
            foreach ($chats as $chat) {
                echo '<div class="chat" onclick="redirect(\''.$chat['id'].'\');">';
                echo '<h1>'.$chat['name'].'</h1>';
                echo '<h3 class="users">'.implode(', ', $chat['users']).'</h3>';
                echo '<p>'.$chat['date'].'</p>';
                echo '</div>';
            }
            echo '</section>';
        } else {
            echo '<div id="mess">';

            $lastsender = '';
            foreach ($mess as $row) {
                if ($row['sender'] != $lastsender) echo '<div class="user '.($row['sender'] == $_GET['id'] ? 'yours' : 'theirs').'"><p>'.select_user($con, $row['sender']).'</p></div>';
                echo '<div class="mess '.($row['sender'] == $_GET['id'] ? 'yours' : 'theirs').'">';
                echo '<p>'.$row['content'].'</p>';
                echo '</div>';
                $lastsender = $row['sender'];
            }

            echo '</div><form autocomplete="off" action="./index.php?id='.$_GET['id'].'&mess='.$_GET['mess'].'&count='.$_GET['count'].'" method="post">';
            echo '<input type="button" class="btn material-symbols-outlined" value="arrow_back" />';
            echo '<input type="text" name="text" id="text" autofocus />';
            echo '<input type="submit" class="btn material-symbols-outlined" value="send" />';
            echo '</form>';
        }
    ?>

    <script> var userid = <?= $_GET['id'] ?>; var messid = <?= isset($_GET['mess']) ? $_GET['mess'] : 0 ?>;</script>
    <script src="app.js"></script>
</body>
</html>