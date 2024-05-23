<?php

function get_mess($con, $mess, $count, $amount = 25) {
    $count = $count - $amount;
    $stmt = $con->prepare("SELECT sender, content, date FROM mess WHERE chat = ? ORDER BY date DESC LIMIT ?, 25;");
    $stmt->bind_param('ii', $mess, $count);
    $stmt->execute();
    $res = $stmt->get_result();

    $mess = [];
    while ($row = $res->fetch_assoc())
        $mess[] = array('sender' => $row['sender'], 'content' => $row['content'], 'date' => $row['date']);
    return array_reverse($mess);
}

function get_mess_json($con, $mess, $count) {
    $count = $count - 25;
    $stmt = $con->prepare("SELECT m.content, m.date, m.sender AS senderID, u.login AS sender FROM mess AS m JOIN users AS u ON u.ID = m.sender WHERE chat = ? ORDER BY date DESC LIMIT ?, 25;");
    $stmt->bind_param('ii', $mess, $count);
    $stmt->execute();
    return json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}

function get_last_mess_json($con, $mess) {
    $stmt = $con->prepare("SELECT m.content, m.date, m.sender AS senderID, u.login AS sender FROM mess AS m JOIN users AS u ON u.ID = m.sender WHERE chat = ? ORDER BY date DESC LIMIT 1;");
    $stmt->bind_param('i', $mess);
    $stmt->execute();
    return json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}

function select_users($con, $user) {
    $stmt = $con->prepare("SELECT login FROM users WHERE ID = ?");
    $stmt->bind_param('i', $user);
    $stmt->execute();
    return $stmt->get_result();
}

function select_user($con, $user) {
    $stmt = $con->prepare("SELECT login FROM users WHERE ID = ?");
    $stmt->bind_param('i', $user);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc())
        return $row['login'];
}

function get_chats($con, $id) {
    $idq = '%:'.$id.':%';
    $stmt = $con->prepare("SELECT ID, name, users, date FROM chats WHERE users LIKE ?");
    $stmt->bind_param('s', $idq);
    $stmt->execute();
    $res = $stmt->get_result();

    while($chat = $res->fetch_assoc()) {
        $usersids = array_filter(explode(':', $chat['users']));
        $users = [];
        foreach($usersids as $user) $users[] = select_user($con, $user);

        $chats[] = array('id' => $chat['ID'], 'name' => $chat['name'], 'users' => $users, 'date' => $chat['date']);
    }

    return isset($chats) ? $chats : null;
}

function alert($text) {
    return '<script>alert("'.$text.'");</script>';
}