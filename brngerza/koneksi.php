<?php
$host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST');
$user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER');
$pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');
$db   = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE');
$port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT');

$koneksi = mysqli_connect($host, $user, $pass, $db, (int)$port);

if (!$koneksi) {
    die('Connection failed: ' . mysqli_connect_error());
}
