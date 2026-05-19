<?php
session_start();
require 'koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];  // store role

        // Redirect based on role
        if($user['role'] == 'admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: laporan3.php'); // or wherever regular users go
        }
        exit();
    } else {
        header('Location: login.php?error=1&message=Username+atau+password+salah');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<?php
if (isset($_SESSION['error'])) {
    echo "<script>alert('{$_SESSION['error']}');</script>";
    unset($_SESSION['error']);
}
?>
    <div class="base">
        <div class="title"><h2>Login Form</h2></div>
        <div class="container">
            <form method="post">
                <div class="uname" style="padding:7px">
                    <label for="uname"><b>Username</b></label><br>
                    <input type="text" placeholder="Enter Username" name="uname" required>
                </div>
                <br>
                <div class="psw" style="padding:7px">
                    <label for="psw"><b>Password</b></label><br>
                    <input type="password" placeholder="Enter Password" name="psw" required>
                </div>
                <br>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
