<?php
require 'auth.php';
require 'koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM anggota WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$member = mysqli_fetch_assoc($result);

// Fix the field names for JavaScript
$response = [
    'nama' => $member['nama'],
    'usaha' => $member['usaha'],
    'no_telp' => $member['no_telp'],
    'email' => $member['email'],
    'alamat' => $member['alamat'],
    'nik' => $member['NIK'],  // Convert uppercase to lowercase
    'ttl' => $member['ttl'],
    'joindate' => $member['joindate']
];

header('Content-Type: application/json');
echo json_encode($response);
?>