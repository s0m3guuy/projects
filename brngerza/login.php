<head>
    <link rel="stylesheet" href="login.css">
    <?php
    session_start();

    if (isset($_SESSION['username'])) {
        header("Location: dashboard.php");
        exit;
    }
    ?>

</head>



<?php
    include 'koneksi.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = $_POST['uname'];
        $password = $_POST['psw'];

        $query = "SELECT * FROM user WHERE username='$username'";
        $result = mysqli_query($koneksi, $query);
        $user = mysqli_fetch_assoc($result);
        if ($user && $password === $user['password']) {
            $_SESSION['username'] = $user['username'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid username or password";
            header("Location: login.php");
            exit;
        }

    }
?>

<?php
if (isset($_SESSION['error'])) {
    echo "<script>alert('{$_SESSION['error']}');</script>";
    unset($_SESSION['error']);
}
?>

<body>
    <div class=base>
            <div class="title">
                <h2>Login Form</h2>
            </div>
            <div class="container">
            <form method="post">
                <div class="uname" style="padding:7px">
                    <label for="uname"><b>Username </b></label></br>
                    <input type="text" placeholder="Enter Username" name="uname" required>
                </div>
                </br>
                <div class="psw" style="padding:7px">
                    <label for="psw"><b>Password  </b></label></br>
                    <input type="password" placeholder="Enter Password" name="psw" required>
                </br>
</div>
</br>
                <button type="submit" name ="login">Login</button>
            </form>
        </div>
    </div>
</body>
