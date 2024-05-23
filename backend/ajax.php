<?php

header('Content-Type: application/json');

if (!isset($_GET['action'])) exit;

$con = require_once './connect.php';
require_once './utils.php';

if ($_GET['action'] == 'generate') echo get_mess_json($con, $_GET['mess'], $_GET['count']);

if ($_GET['action'] == 'send' && isset($_GET['content']) && strlen($_GET['content']) > 0 && strlen($_GET['content']) <= 255 && isset($_GET['mess'])) {
    $stmt = $con->prepare("INSERT INTO mess(sender, chat, content) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $_GET['id'], $_GET['mess'], $_GET['content']);
    $stmt->execute();
}

if ($_GET['action'] == 'refresh') echo get_last_mess_json($con, $_GET['mess']);

if ($_GET['action'] == 'add' && isset($_GET['add']) && isset($_GET['mess'])) {
    $stmt = $con->prepare("SELECT users FROM chats WHERE ID = ?;");
    $stmt->bind_param('i', $_GET['mess']);
    $stmt->execute();
    $res = $stmt->get_result();
    $users = $res->fetch_assoc()['users'].$_GET['add'].":";

    $stmt = $con->prepare("UPDATE chats SET users = ? WHERE ID = ?;");
    $stmt->bind_param('si', $users, $_GET['mess']);
    $stmt->execute();
}

if ($_GET['action'] == 'create' && isset($_GET['group']) && isset($_GET['users'])) {
    $stmt = $con->prepare("INSERT INTO chats(name, users) VALUES (?, ?)");
    $stmt->bind_param('ss', $_GET['group'], $_GET['users']);
    $stmt->execute();
    echo json_encode($_GET['group'].' '.$_GET['users']);
}

// $con->close();
// exit;