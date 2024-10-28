<?php

include 'auth.class.php';

$username = "root";
$pass = "";
$server = "localhost";
$name = "ums";
$table = "users";

/**
 * For register
 */
$user = new user($username, $pass, $name, $server, $table);
switch ($user->register("serohan", "123123", "192.192.192.21", "Mozilla/5.0")) {
    case -1:
        echo 'Username already taken';
        break;
    case -2:
        echo 'Multi-Account';
        break;
    case true:
        echo 'Success';
        break;
    default:
        echo 'Database error';
        break;
}
 

/**
 * For Login
 */
$user = new user($username, $pass, $name, $server, $table);
switch ($user->login("serohan", "123123", "192.192.192.21")) {
    case -1:
        echo 'Wrong Password';
        break;
    case -2:
        echo 'Non-existing account';
        break;
    case true:
        echo 'Success';
        break;
    default:
        echo 'Database error';
        break;
}
?>
