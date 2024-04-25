<?php

session_start();

function thrErr(string $err): void {
    $_SESSION['err'] = $err;
    header('Location: ./');
    exit;
}

$login = $_POST['login'];
$pass = $_POST['pass'];

$_SESSION['login'] = $login;

if (!ctype_alpha($login) || !ctype_graph($login))
    thrErr('Incorrect characters in login, use 0-9, a-z or A-Z characters');

if (strlen($login) > 20)
    thrErr('Username is too long, max length is 20 characters');

$con = require_once '../backend/connect.php';

$stmt = $con->prepare("SELECT ID, pass FROM users WHERE login = ? OR email = ?;");
$stmt->bind_param('ss', $con->escape_string($login), $con->escape_string($email));
$stmt->execute();
$res = $stmt->get_result();

$user = $res->fetch_assoc();
if (!password_verify($pass, $user['pass']))
    thrErr('Incorrect username or password');

$_SESSION['logged'] = true;
$_SESSION['ID'] = $user['ID'];
header('location: ../home/index.php?id='.$user['ID']);
exit;