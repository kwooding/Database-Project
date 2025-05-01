<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Force a clean IPv4/TCP connect:
$host = '127.0.0.1';  
$port = 3307;        // matches your my.ini

$user = 'root';
$pass = '';
$db   = 'termdatabase';

$mysqli = new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die(
      "Connect Error ({$mysqli->connect_errno}): "
      . htmlspecialchars($mysqli->connect_error)
    );
}
