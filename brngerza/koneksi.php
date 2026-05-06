<?php
$host = getenv('MYSQLHOST')     ?: 'localhost';
$user = getenv('MYSQLUSER')     ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$port = getenv('MYSQLPORT')     ?: 3306;

$koneksi = mysqli_connect($host, $user, $pass, $db, (int)$port);

if(!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}
