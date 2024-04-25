<?php

session_start();

function thrErr(string $err): void {
    $_SESSION['err'] = $err;
    header('Location: ./');
    exit;
}

$login = $_POST['login'];
$email = $_POST['email'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];

$_SESSION['login'] = $login;
$_SESSION['email'] = $email;

if (!ctype_alpha($login) || !ctype_graph($login))
    thrErr('Incorrect characters in login, use 0-9, a-z or A-Z characters');

if (strlen($login) > 20)
    thrErr('Username is too long, max length is 20 characters');

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    thrErr('Incorrect email');

if ($pass1 != $pass2)
    thrErr('Password didn\'t match');

if (strlen($pass1) < 8 || strlen($pass1) > 20)
    thrErr('Incorrect password length, correct length is between 8 and 20 characters');

$con = require_once '../backend/connect.php';

$stmt = $con->prepare("SELECT ID FROM users WHERE login = ? OR email = ?;");
$stmt->bind_param('ss', $con->escape_string($login), $con->escape_string($email));
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows != 0)
    thrErr('Login or email is in use');

$stmt = $con->prepare("INSERT INTO users(login, email, pass) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $con->escape_string($login), $con->escape_string($email), password_hash($pass1, PASSWORD_DEFAULT));
$stmt->execute();

$stmt = $con->prepare("SELECT ID FROM users WHERE login = ?");
$stmt->bind_param('s', $login);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()['ID'])
    $id = $row['ID'];

$_SESSION['logged'] = true;
$_SESSION['ID'] = $id;
header('location: ../home/index.php?id='.$id);
exit;