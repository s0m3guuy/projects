<?php
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE'); // this will return 'railway'
$port = getenv('MYSQLPORT');

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die('Connection failed: ' . mysqli_connect_error());
}
