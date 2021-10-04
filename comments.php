<?php
// Welcome to members page 
// here you can Approve | delete | update a comment 
session_start();
$pageTitle = "members";
if (isset($_SESSION['Username'])) {
    // Include The Important Files
    include 'init.php';
    $pdo = pdoConnectMysql();
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if ($do == "manage") {
        //manage page
        $stmt = $pdo->prepare("SELECT comments.*, Items.Name AS ItemName, users.Username AS USERNAME
         FROM comments 
         JOIN items
            ON items.Item_ID = comments.item_id
         JOIN users
            ON users.UserID = comments.user_id
            ORDER BY  c_id DESC
         ");
        $stmt->execute();
        //save result as array to var 
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($comments)) {
?>
            <h1 class="display-4 text-center mr-auto ml-auto">manage comments</h1>
            <div class="container mt-4">
                <table class="table table-sm table-striped table-bordered main-table">
                    <thead>
                        <tr>
                            <td>Comment ID</td>
                            <td>Comment</td>
                            <td>Comment Date</td>
                            <td>Item Name</td>
                            <td>User</td>
                            <td>Operation</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment) : ?>
                            <tr>
                                <td><?= $comment['c_id'] ?></td>
                                <td><?= $comment['comment'] ?></td>
                                <td><?= $comment['comment_date'] ?></td>
                                <td><?= $comment['ItemName'] ?></td>
                                <td><?= $comment['USERNAME'] ?></td>
                                <td class="actions btn-group-sm p-2">
                                    <a href="comments.php?do=edit&commentId=<?php echo $comment['c_id'] ?>" class="btn btn-secondary">
                                        <i class="icon fas fa-edit fa-xs"></i>
                                        edit</a>
                                    <a href="comments.php?do=delete&commentId=<?php echo $comment['c_id'] ?>" class="btn btn-danger confirm ">
                                        <i class="icon fas fa-trash fa-xs"></i>delete</a>
                                    <?php if ($comment['status'] == 0) : ?>
                                        <a href="comments.php?do=approve&commentId=<?php echo $comment['c_id'] ?>" class="btn btn-info">
                                            <i class="icon fas fa-check fa-xs"></i>
                                            Approve</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php   } else {
            redirectHome("no comments to be shown !", 'back', 4, "alert alert-info");
        }
    } else if ($do == 'edit') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $commentId = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;
        $stmt = $pdo->prepare('SELECT * FROM comments WHERE c_id = ? ');
        $stmt->execute([$commentId]);
        $row = $stmt->fetch();
        // if a member exist then the display the from...display error instead if the id doesn't exist
        if ($stmt->rowCount() > 0) { ?>
            <div class="ml-auto mr-auto container w-50 ">
                <h1 class="h1 text-center p-3">Edit Comment</h1>
                <form class="form" method="post" action="?do=update">
                    <!-- hidden input for update is the record on the db -->
                    <input type="hidden" name="commentId" value="<?php echo $commentId ?>">
                    <div class="form-group ">
                        <label for=" Username">Comment</label>
                        <textarea type="text" name="comment" class="form-control" required="required"><?php echo $row['comment'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary px-4">Save</button>
                    </div>
                </form>
            </div>
<?php
        } else {
            // which means that the id doesn't exist 
            $err = "comment with that id doesn't exist !";
            redirectHome($err, 'back', 4);
        }
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //hold the user id from hidden input 
            $commentId = $_POST['commentId'];
            // hold the comment value
            $comment = filterString($_POST['comment']);
            // if none then go ahead and update the member
            if ($comment) {
                // update the user 
                $query = 'UPDATE comments SET comment = ? WHERE c_id = ?';
                $stmt = $pdo->prepare($query);
                if ($stmt->execute([$comment, $commentId])) {
                    // echo success message
                    $msg =  $stmt->rowCount() . ' has been updated';
                    redirectHome($msg, 'back', 3, 'alert alert-success');
                } else {
                    $err = 'something went wrong: ' . $pdo->errorInfo();
                    redirectHome($err, 'back', 4, 'alert alert-danger');
                }
            }
        } else {
            $err = 'You cannot browse this page directly';
            redirectHome($err, 'back', 3, 'alert alert-warning');
        }
    } else if ($do == 'approve') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $commentId = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;
        // check if the user exist based on userId
        $check = checkItem('c_id', 'comments', $commentId);
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('UPDATE comments SET status = 1  WHERE c_id = ?');
            if ($stmt->execute([$commentId])) {
                $msg = ' Comment has been Approved successfully! ';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error approving while activating,please try again : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-info');
            }
        } else {
            $error = 'Comment with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    } else if ($do == 'delete') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $commentId = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;
        // check if the user exist based on userId
        $check = checkItem('c_id', 'comments', $commentId);
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('DELETE FROM comments WHERE c_id = ?');
            if ($stmt->execute([$commentId])) {
                $msg = 'record has been deleted successfully !';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error accurred while deleting : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-danger');
            }
        } else {
            $error = 'Comment with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    }
    include $template . 'footer.php';
} else {
    header('Location: index.php');
    exit;
}

?>