<?php
// Welcome to members page 
// here you can add delete update a user 
session_start();
$pageTitle = "members";
if (isset($_SESSION['Username'])) {
    // Include The Important Files
    include 'init.php';
    $pdo = pdoConnectMysql();
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if ($do == "manage") {
        // we'll nest some code for redirecting to pending members
        $query = '';
        if (isset($_GET['page']) && $_GET['page'] == 'pending') {
            $query = "AND RegStatus = 0";
        }
        //manage page
        //Select all users except admins
        $stmt = $pdo->prepare("SELECT * FROM users WHERE GroupID <> 1 $query  ORDER BY UserID DESC");
        $stmt->execute();
        //save result as array to var 
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
?>
            <h1 class="display-4 text-center mr-auto ml-auto">manage members</h1>
            <div class="container mt-4 mr-auto ml-auto w-75">
                <a href='members.php?do=add' class='btn btn-primary mb-1'>
                    <i class='icon fa fa-plus'></i>
                    Add New Member</a>
                <table class="table table-sm table-bordered table-striped main-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Username</td>
                            <td>Email</td>
                            <td>Full name</td>
                            <td>registred date</td>
                            <td>operations</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <td><?= $row['UserID'] ?></td>
                                <td><?= $row['Username'] ?></td>
                                <td><?= $row['Email'] ?></td>
                                <td><?= $row['FullName'] ?></td>
                                <td><?= $row['Date'] ?></td>
                                <td class="actions">
                                    <a href="members.php?do=edit&userId=<?php echo $row['UserID'] ?>" class="btn btn-secondary"><i class="icon fas fa-edit fa-xs "></i>edit</a>
                                    <a href="members.php?do=delete&userId=<?php echo $row['UserID'] ?>" class="btn btn-danger confirm"><i class="icon fas fa-trash fa-xs"></i>delete</a>
                                    <?php if ($row['RegStatus'] == 0) : ?>
                                        <a href="members.php?do=activate&userId=<?php echo $row['UserID'] ?>" class="btn btn-info"><i class="icon fas fa-mark fa-xs"></i>Activate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        <?php   } else {
            echo "<div class'container ml-auto mr-auto text-center alert alert-info'>
            <p class='lead '>there is no record to be shown !</p>
            <a href='members.php?do=add' class='btn btn-primary'>
                    <i class='icon fa fa-plus'></i>
                    Add New Member</a>
            </div>";
        }
    } else if ($do == 'add') {
        ?>
        <div class="ml-auto mr-auto container w-50 ">
            <h1 class="h1 text-center text-capitalize p-3">add new member</h1>
            <form class="form" method="post" action="?do=insert">

                <div class="form-group">
                    <label for=" Username">Username</label>
                    <input type="text" name="username" class="form-control" required="required" placeholder="use your username to login once you created">
                </div>
                <div class="form-group">
                    <label for="Password">password</label>
                    <input type="password" name="password" class="form-control password" required="required" placeholder="your password has to be between 8 and 20 character">
                    <i class="show-pass fa fa-eye fa-2x text-dark">show</i>
                </div>
                <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" name="email" class="form-control" required="required" placeholder="enter a valid email">
                </div>
                <div class="form-group ">
                    <label for="Fullname">Full name</label>
                    <input type="text" name="fullname" class="form-control" required="required" placeholder="your full name is to identify the user">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-block">add member</button>
                </div>
            </form>
        </div>
        <?php
    } else if ($do == 'insert') {

        echo '<h1 class="h1 text-center text-capitalize p-3">Add member</h1>';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // store all errors through an array
            $errors = array();
            //validate username
            if (empty($_POST['username'])) {
                $errors[] = 'username is required !';
            } else {
                $username = filterString($_POST['username']);
                if (!$username) {
                    $errors[] = 'your username is not valid !';
                }
            }
            //validate Password
            $pass = filterPassword($_POST['password']);
            if (!$pass) {
                $errors[] = 'your password must contains [a-z] and [0-9]and at least one symbol [@*$...]';
            } else if (strlen($pass) < 8 and strlen($pass) < 20) {
                $errors[] = 'Your password must be lesser than 20 characters and bigger than 6 characters';
            } else {
                $hashedpass = sha1($_POST['password']);
            }
            // validate the email
            if (empty($_POST['email'])) {
                $errors[] = 'email is required !';
            } else {
                $email = filterEmail($_POST['email']);
                if (!$email) {
                    $errors[] = "your email is not valid ";
                }
            }
            //validate full name
            if (empty($_POST['fullname'])) {
                $errors[] = "your full name is required !";
            } else {
                $fullname = filterName($_POST['fullname']);
                if (!$fullname) {
                    $errors[] = 'your full name is not valid';
                }
            }
            // check for errors 
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            // if none then go ahead and update the member
            if (empty($errors)) {
                // check if the username is already exist or not
                $exist = checkItem('Username', 'users', $username);
                if ($exist == 1) {
                    redirectHome('username is already exist !', 'back', 4);
                } else {
                    // update the user 
                    $query = 'INSERT INTO users(Username,Password,Email,FullName,RegStatus) VALUES (?,?,?,?,?)';
                    $stmt = $pdo->prepare($query);
                    if ($stmt->execute([$username, $hashedpass, $email, $fullname, 1])) {
                        // echo success message
                        $msg =   $stmt->rowCount() . ' member has been Added !';
                        redirectHome($msg, 'back', 3, 'alert alert-success');
                    } else {
                        $err = 'something went wrong: ' . $stmt->errorInfo();
                        redirectHome($err, 'back', 3, 'alert alert-danger');
                    }
                }
            }
        } else {
            $err = 'You cannot browse this page directly';
            redirectHome($err, 'back', 3, 'alert alert-warning');
        }
    } else if ($do == 'edit') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $userId = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE UserID = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        // if a member exist then the display the from...display error instead if the id doesn't exist
        if ($stmt->rowCount() > 0) { ?>
            <div class="ml-auto mr-auto container w-50 ">
                <h1 class="h1 text-center text-capitalize p-3">edit member</h1>
                <form class="form" method="post" action="?do=update">
                    <!-- hidden input for update is the record on the db -->
                    <input type="hidden" name="userId" value="<?php echo $userId ?>">
                    <div class="form-group ">
                        <label for=" Username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $row['Username'] ?>" required="required">
                    </div>
                    <div class="form-group ">
                        <label for="Password">password</label>
                        <input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>">
                        <input type="password" name="newpassword" class="form-control" placeholder="leave it blank if you don't wanna change it">
                    </div>
                    <div class="form-group ">
                        <label for="Email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $row['Email'] ?>" required="required">
                    </div>
                    <div class="form-group ">
                        <label for="Fullname">Full name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo $row['FullName'] ?>" required="required">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block">Save member</button>
                    </div>
                </form>
            </div>
<?php
        } else {
            // which means that the id doesn't exist 
            $err = "member with that id doesn't exist !";
            redirectHome($err, 'back', 4);
        }
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //hold the user id from hidden input 
            $Id = $_POST['userId'];
            // store all errors through an array
            $errors = array();
            //validate username
            if (empty($_POST['username'])) {
                $errors[] = 'username is required !';
            } else {
                $username = filterString($_POST['username']);
                if (!$username) {
                    $errors[] = 'your username is not valid !';
                }
            }
            //validate Password 
            $pass = '';
            if (empty($_POST['newpassword'])) { // if user doesn't insert new password then save old one
                $pass = $_POST['oldpassword'];
            } else { // else filter the new password
                $pass = filterPassword($_POST['newpassword']);
                if (!$pass) {
                    $errors[] = 'your password must contains [a-z] and [0-9] and at least one symbol [@*$...]';
                } else if (strlen($pass) < 8 and strlen($pass) < 20) {
                    $errors[] = 'Your password must be lesser than 20 characters and greater than 6 characters';
                } else {
                    $pass = sha1($_POST['newpassword']);
                }
            }
            // validate the email
            if (empty($_POST['email'])) {
                $errors[] = 'email is required !';
            } else {
                $email = filterEmail($_POST['email']);
                if (!$email) {
                    $errors[] = "your email is not valid ";
                }
            }
            //validate full name
            if (empty($_POST['fullname'])) {
                $errors[] = "your full name is required !";
            } else {
                $fullname = filterName($_POST['fullname']);
                if (!$fullname) {
                    $errors[] = 'your full name is not valid';
                }
            }
            // check for errors 
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            // if none then go ahead and update the member
            if (empty($errors)) {
                // will do a another check for username intergrity
                $stmt2 = $pdo->prepare("SELECT * FROM users WHERE Username= ? AND UserID <> ?");
                $stmt2->execute([$username, $Id]);
                if ($stmt2->rowCount() == 1) {
                    redirectHome("Member with that username already exist !", 'back');
                } else {
                    // update the user 
                    $query = 'UPDATE users SET Username = ?, Password = ?, Email= ?, FullName = ? WHERE UserID = ?';
                    $stmt = $pdo->prepare($query);
                    if ($stmt->execute([$username, $pass, $email, $fullname, $Id])) {
                        // echo success message
                        $msg =  $stmt->rowCount() . ' has been updated';
                        redirectHome($msg, 'back', 3, 'alert alert-success');
                    } else {
                        $err = 'something went wrong: ' . $pdo->errorInfo();
                        redirectHome($err, 'back', 4, 'alert alert-danger');
                    }
                }
            }
        } else {
            $err = 'You cannot browse this page directly';
            redirectHome($err, 'back', 3, 'alert alert-warning');
        }
    } else if ($do == 'activate') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $userId = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;
        // check if the user exist based on userId
        $check = checkItem('UserID', 'users', $userId);
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('UPDATE users SET RegStatus = 1  WHERE UserID = ?');
            if ($stmt->execute([$userId])) {
                $msg = ' record has been activated successfully! ';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error accurred while activating,please try again : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-info');
            }
        } else {
            $error = 'member with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    } else if ($do == 'delete') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $userId = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;
        // check if the user exist based on userId
        $check = checkItem('UserID', 'users', $userId);
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('DELETE FROM users WHERE UserID = ?');
            if ($stmt->execute([$userId])) {
                $msg = ' record has been deleted successfully !';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error accurred while deleting : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-danger');
            }
        } else {
            $error = 'member with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    }
    include $template . 'footer.php';
} else {
    header('Location: index.php');
    exit;
}

?>