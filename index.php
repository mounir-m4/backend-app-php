<?php
$noNavbar = '';
$pageTitle = "index";
include 'init.php';
//start a session to store data through 
session_start();
if (isset($_SESSION['Username'])) {
    header('Location: dashboard.php');
}
// vars to hold user inputs and manage errors 
$username = $password = $usernameErr = $passwordErr = $hashedpass = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['username'])) {
        $usernameErr = "username is required";
    } else {
        $username = filterString($_POST['username']);
        if ($username === false) {
            $usernameErr = "username isn't valid";
        }
    }
    if (empty($_POST['password'])) {
        $passwordErr = "password is required ";
    } else {
        $password = filterString($_POST['password']);
        if ($password === false) {
            $passwordErr = "insert a valid password ";
        } else {
            $hashedpass = sha1($password);
        }
    }

    if (empty($usernameErr) && empty($passwordErr)) {
        $pdo = pdoConnectMysql();
        $sql = 'SELECT 
                        UserID,Username,Password
                         FROM users
                          WHERE
                           Username = ?
                            and Password = ?
                             AND GroupID = 1 
                             LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($username, $hashedpass));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();
        //if rowCount > 0 : db contains records about this username
        if ($count > 0) {
            //start a session 
            $_SESSION['Username'] = $username; // save session name
            $_SESSION['ID'] = $row['UserID'];
            header('Location: dashboard.php'); // redirect to dashboard 
            exit;
        } else {
            $passwordErr = "username or password isn't correct";
        }
    }
}
?>


<div class="content ml-auto mr-auto">
    <form id="form" class="form-log" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
        <h2 class="heading">Admin Login</h2>
        <div class="form-controls">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="enter username" value="<?php echo isset($_POST['username']) ? $username : '' ?>" />
            <small class="error"><?php echo $usernameErr ?></small>
        </div>
        <div class="form-controls">
            <label for="username">Password</label>
            <input type="password" name="password" placeholder="enter a password" value="<?php echo isset($_POST['password']) ? $password : "" ?>" />
            <small class="error"><?php echo $passwordErr ?></small>
        </div>
        <button class="submit" type="submit">Login</button>
    </form>
</div>
<?php include $template . 'footer.php' ?>