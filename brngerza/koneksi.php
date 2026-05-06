<?php
$host   = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$user   = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$pass   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$db     = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'railway';
$port   = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: 3306;

$koneksi = mysqli_connect($host, $user, $pass, $db, (int)$port);

if(!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
