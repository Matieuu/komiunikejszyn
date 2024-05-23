<?php

session_start();

if (!isset($_SESSION['logged']) || !isset($_SESSION['ID']) || !$_SESSION['logged'] || !isset($_GET['id'])) {
    session_unset();
    header('Location: ../login/');
    exit;
}

if ($_SESSION['ID'] != $_GET['id']) {
    header('Location: ./index.php?id='.$_SESSION['ID']);
    exit;
}

$con = require_once '../backend/connect.php';
require_once '../backend/utils.php';

if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}

$chats = get_chats($con, $_GET['id']);
if (isset($_GET['mess'])) $mess = get_mess($con, $_GET['mess'], $_GET['count'], $_GET['count']);
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
        $hasID = isset($chats) ? count(array_filter($chats, function($chat) { 
            return isset($chat) && isset($_GET['mess']) ? $chat['id'] == $_GET['mess'] : null; 
        })) > 0 : false;
        if (!isset($_GET['mess'])) {
            echo '<section>';
            if (isset($chats)) 
                foreach ($chats as $chat) {
                    echo '<div class="chat" onclick="redirect(\''.$chat['id'].'\');">';
                    echo '<h1>'.$chat['name'].'</h1>';
                    echo '<h3 class="users">'.implode(', ', $chat['users']).'</h3>';
                    echo '<p>'.$chat['date'].'</p>';
                    echo '</div>';
                }
            else echo '<p id="nochats">No chats, talk to friends irl :p</p>';
            echo '</section>';
            echo '<form autocomplete="off"><input type="button" class="btn material-symbols-outlined" value="add" onclick="create()" /></form>';
        } elseif(!$hasID) {
            $con->close();
            header('location: ./index.php?id='.$_GET['id']);
            exit;
        } else {
            echo '<div id="mess">';

            $lastsender = '';
            foreach ($mess as $row) {
                if ($row['sender'] != $lastsender) echo '<div class="user '.($row['sender'] == $_GET['id'] ? 'yours' : 'theirs').'"><p>'.select_user($con, $row['sender']).'</p></div>';
                echo '<div class="mess '.($row['sender'] == $_GET['id'] ? 'yours' : 'theirs').'">';
                echo '<p>'.$row['content'].'</p>';
                echo '</div>';
                $lastsender = $row['sender'];
                $lastmess = $row['content'];
            }

            echo '</div><form autocomplete="off">';
            echo '<input type="button" class="btn material-symbols-outlined" value="arrow_back" onclick="back()" />';
            echo '<input type="button" class="btn material-symbols-outlined" value="add" onclick="add()" />';
            echo '<input type="text" name="text" id="text" autofocus />';
            echo '<input type="button" class="btn material-symbols-outlined" value="send" id="sender" />';
            echo '</form>';
        }
    ?>

    <script> 
    var userid = <?= $_GET['id'] ?>; 
    var messid = <?= isset($_GET['mess']) ? $_GET['mess'] : 0 ?>;
    var lastMess = { sender: "<?= isset($lastsender) ? $lastsender : '' ?>", content: "<?= isset($lastmess) ? $lastmess : '' ?>" };
    <?= isset($_SESSION['users']) ? "console.log('".$_SESSION['users']."');" : '' ?>
    </script>
    <script src="app.js"></script>
</body>
</html>
<?php

$con->close();