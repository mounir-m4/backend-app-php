<?php
session_start();
$pageTitle = "dashboard";
if (!isset($_SESSION['Username'])) {
    header('Location: index.php');
}
// Include The Important Files
include 'init.php';
$numUsers = 3;
$numComments = 3;
$numItems = 3;
// import the last added users from db
$users = getLatest('*', 'users', 'UserID', $numUsers);
// import the last added items from db
$items = getLatest('*', 'items', 'Item_ID', $numItems);

$totalComments = getLatest("*", "comments", 'c_id', $numComments);
// init connection
$pdo = pdoConnectMysql();
?>
<!-- Start dashboard -->
<div class="container">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</div>
<div class="container home-state">
    <div class="row text-center">
        <div class="col-xl-3 col-md-6 ">
            <div class="state">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="icons">
                            <span><i class="fa fa-users"></i></span>
                        </div>
                        Total Members
                        <span><?php echo countItems('UserID', 'users') ?></span>
                    </div>
                    <div class="card-footer">
                        <div class="text-white">
                            <a href="members.php" class="text-white text-decoration-none">
                                <i class="fas fa-arrow-right mr-1"></i>
                                View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 ">
            <div class="state">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="icons">
                            <span><i class="fa fa-user-plus"></i></span>
                        </div>
                        Pending Members
                        <span>
                            <?php echo checkItem('RegStatus', 'users', 0) ?>
                        </span>
                    </div>
                    <div class="card-footer">
                        <div class="text-white">
                            <a href="members.php?do=manage&page=pending" class="text-white text-decoration-none">
                                <i class="fas fa-arrow-right mr-1"></i>
                                View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 ">
            <div class="state">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="icons">
                            <span><i class="fa fa-tag"></i></span>
                        </div>
                        Total Items
                        <span><?php echo countItems('Item_ID', 'items') ?></span>
                    </div>
                    <div class="card-footer">
                        <div class="text-white">
                            <a href="items.php?do=manage" class="text-white text-decoration-none">
                                <i class="fas fa-arrow-right mr-1"></i>
                                View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 ">
            <div class="state">
                <div class="card bg-dark text-white text-center">
                    <div class="card-body">
                        <div class="icons">
                            <span><i class="fa fa-comments"></i></span>
                        </div>
                        Total Comments
                        <span><?php echo countItems('c_id', 'comments') ?></span>
                    </div>
                    <div class="card-footer">
                        <div class="text-white">
                            <a href="comments.php?do=manage" class="text-white text-decoration-none">
                                <i class="fas fa-angle-right mr-1"></i>
                                View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container latest">
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header">
                    <i class="fa fa-users mr-1"></i>
                    Lastest <?= $numUsers ?> added users
                    <span class="toggle-menu">
                        <i class="fa fa-minus float-right"></i>
                    </span>
                </div>
                <div class="body-menu">
                    <ul class="latest-users list-group list-group-flush list-unstyled">
                        <?php
                        if (!empty($users)) {
                            foreach ($users as $user) {
                                echo '<li class="list-group-item list-group-item-light text-dark">' . $user['FullName'] .
                                    '<span class="btn btn-danger float-right btn-sm">
                                <i class="fa fa-trash"></i>
                                <a class="text-decoration-none text-light" href="members.php?do=delete&userId=' . $user['UserID'] . ' ">delete</a>
                                </span>';
                                echo '<span class="btn btn-secondary float-right mr-1 btn-sm">
                                <i class="fa fa-edit"></i>
                                <a class="text-decoration-none text-light" href="members.php?do=edit&userId=' . $user['UserID'] . ' ">edit</a>
                                </span>';
                                if ($user['RegStatus'] == 0) {
                                    echo '<span class="btn btn-info float-right mr-1 btn-sm">
                                         <i class="fa fa-success"></i>
                                     <a class="text-decoration-none text-light" href="members.php?do=activate&userId=' . $user['UserID'] . ' ">activate</a>
                                      </span>';
                                }
                                '</li>';
                            }
                        } else {
                            echo "<p class='lead p-2'>no users yet</p>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header">
                    <i class="fa fa-tag mr-1"></i>
                    Lastest <?= $numItems ?> added items
                    <span class="toggle-menu">
                        <i class="fa fa-minus float-right"></i>
                    </span>
                </div>
                <div class="body-menu">
                    <ul class="latest-users list-group list-group-flush list-unstyled">
                        <?php
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                echo '<li class="list-group-item list-group-item-light text-dark">' . $item['Name'] .
                                    '<span class="btn btn-danger float-right btn-sm">
                                <i class="fa fa-trash"></i>
                                <a class="text-decoration-none text-light btn-sm" href="items.php?do=delete&itemId=' . $item['Item_ID'] . ' ">delete</a>
                                </span>';
                                echo '<span class="btn btn-secondary float-right mr-1 btn-sm">
                                <i class="fa fa-edit"></i>
                                <a class="text-decoration-none text-light btn-sm" href="items.php?do=edit&itemId=' . $item['Item_ID'] . ' ">edit</a>
                                </span>';
                                if ($item['Approve'] == 0) {
                                    echo '<span class="btn btn-info float-right mr-1 btn-sm">
                                     <i class="fa fa-check"></i>
                                 <a class="text-decoration-none text-light" href="items.php?do=approve&itemId=' . $item['Item_ID'] . ' ">Approve</a>
                                  </span>';
                                }
                                '</li>';
                            }
                        } else {
                            echo "<p class='lead p-2'>no items yet</p>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card bg-light">
                <div class="card-header">
                    <i class="fa fa-comments mr-1"></i>
                    Lastest <?= $numComments ?> added comments
                    <span class="toggle-menu">
                        <i class="fa fa-minus float-right"></i>
                    </span>
                </div>
                <div class="card-body c-body body-menu">
                    <?php
                    $stmt = $pdo->prepare("SELECT comments.*, users.Username AS USERNAME
                    FROM comments
                    JOIN users
                    ON users.UserID = comments.user_id
                    ORDER BY c_id DESC
                    LIMIT $numComments
                    ");
                    $stmt->execute();
                    $comments =  $stmt->fetchAll();
                    if (!empty($comments)) {
                        foreach ($comments as $comment) {
                            echo '<div class="comment-box">';
                            echo '<span class="member-name">
													<a class="users p-2" href="members.php?do=edit&userId=' . $comment['user_id'] . '">
														' . $comment['USERNAME'] . '</a></span>';
                            echo "<p class='member-comment'>" . $comment['comment'] . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='lead p-2'>no comments yet</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End dashboard -->
<?php include $template . 'footer.php'; ?>