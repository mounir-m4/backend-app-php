<?php
session_start();
$pageTitle = "title";
if (isset($_SESSION['Username'])) {
    // Include The Important Files
    include 'init.php';
    $pdo = pdoConnectMysql();
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if ($do == "manage") {
        //manage page
        //Select all users except admins
        $stmt = $pdo->prepare("SELECT
                                    items.*,
                                    categories.Name AS Category ,
                                    users.Username AS Member 
                               FROM
                                    items
                               JOIN categories
                               ON   categories.ID = items.Cat_ID
                               JOIN
                                    users
                               ON   users.UserID = Items.Member_ID");
        $stmt->execute();
        //save result as array to var 
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($items)) {
?>
            <h1 class="display-4 text-center mr-auto ml-auto">manage items</h1>
            <div class="container mt-4">
                <a href="items.php?do=add" class="btn btn-primary mb-1"><i class="icon fa fa-plus"></i>Add New Item</a>
                <table class="table table-sm table-bordered table-striped main-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Price</td>
                            <td>added date</td>
                            <td>Category</td>
                            <td>Member</td>
                            <td>operations</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= $item['Item_ID'] ?></td>
                                <td><?= $item['Name'] ?></td>
                                <td><?= $item['Description'] ?></td>
                                <td><?= $item['Price'] ?><span class="ml-1">&dollar;</span></td>
                                <td><?= $item['Add_Date'] ?></td>
                                <td><?= $item['Category'] ?></td>
                                <td><?= $item['Member'] ?></td>
                                <td class="actions">
                                    <a href="items.php?do=edit&itemId=<?php echo $item['Item_ID'] ?>" class="btn btn-secondary btn-sm ">
                                        <i class="fa fa-edit"></i>
                                        edit</a>

                                    <a href="items.php?do=delete&itemId=<?php echo $item['Item_ID'] ?>" class="btn btn-danger btn-sm confirm ">
                                        <i class="fa fa-trash"></i>
                                        delete</a>
                                    <?php if ($item['Approve'] == 0) : ?>
                                        <a href="items.php?do=approve&itemId=<?php echo $item['Item_ID'] ?>" class="btn btn-info btn-sm ">
                                            <i class="fa fa-check"></i>
                                            Approve</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        <?php  } else {
            echo "<div class='mt-4 container ml-auto mr-auto text-center w-50 alert alert-info'>
                <p class='lead p-3'>there is no record to be shown !</p>
                <a href='items.php?do=add' class='btn btn-primary'>
                <i class='icon fa fa-plus'></i>
                Add New Item</a>
                </div>";
        }
    } else if ($do == 'add') { ?>
        <div class="ml-auto mr-auto container w-50 ">
            <h1 class="h1 text-center text-capitalize p-3">add new item</h1>
            <form class="form" method="post" action="?do=insert">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" required="required" placeholder="the name of the item">
                </div>
                <div class="form-group ">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" required="required" placeholder="Describe the item"></textarea>
                </div>
                <div class="form-group ">
                    <label for="price">Price</label>
                    <input type="text" name="price" class="form-control" required="required" placeholder="the price of the item ">
                </div>
                <div class="form-group ">
                    <label for="price">Country</label>
                    <input type="text" name="country" class="form-control" required="required" placeholder="which country the item were made ?">
                </div>
                <div class="form-group ">
                    <label for="price">Status</label>
                    <select name="status" class="form-control">
                        <option value="1">Brand New</option>
                        <option value="2">Like New</option>
                        <option value="3">Old</option>
                        <option value="4">Very Old</option>
                    </select>
                </div>
                <div class="form-group ">
                    <label for="price">Member</label>
                    <select name="member" class="form-control">
                        <?php
                        // calling the users to display em at the option field
                        $stmt = $pdo->prepare('Select * from users');
                        $stmt->execute();
                        $members = $stmt->fetchAll();
                        foreach ($members as $member) {
                            echo "<option value=" . $member['UserID'] . ">" . $member['Username'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group ">
                    <label for="price">Category</label>
                    <select name="category" class="form-control">
                        <?php
                        // calling the users to display em at the option field
                        $stmt = $pdo->prepare('Select * from categories');
                        $stmt->execute();
                        $categories = $stmt->fetchAll();
                        foreach ($categories as $category) {
                            echo "<option value=" . $category['ID'] . ">" . $category['Name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-block">add item</button>
                </div>
            </form>
        </div>
        <?php } else if ($do == 'insert') {
        echo '<h1 class="h1 text-center text-capitalize p-3">Add item</h1>';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // store all errors through an array
            $errors = array();
            //validate item name
            if (empty($_POST['name'])) {
                $errors[] = 'item name is required !';
            } else {
                $name = filterString($_POST['name']);
                if (!$name) {
                    $errors[] = 'item\'s name is not valid !';
                }
            }
            //validate description
            if (empty($_POST['description'])) {
                $errors[] = 'description required !';
            } else {
                $description = filterString($_POST['description']);
                if (!$description) {
                    $errors[] = 'item\'s name is not valid !';
                }
            }
            // validate the email
            if (empty($_POST['price'])) {
                $errors[] = 'price is required !';
            } else {
                $price = filterNumber($_POST['price']);
                if (!$price) {
                    $errors[] = "your price is not valid ";
                }
            }
            // validate country
            //validate full name
            if (empty($_POST['country'])) {
                $errors[] = "country name is required !";
            } else {
                $country = filterString($_POST['country']);
                if (!$country) {
                    $errors[] = 'country name is not valid';
                }
            }
            // validate status
            if ($_POST['status'] !== 0) {
                $status = $_POST['status'];
            } else {
                $errors = 'Please select a status ';
            }
            // validate Member
            if ($_POST['member'] !== 0) {
                $member = $_POST['member'];
            } else {
                $errors = 'Please select a member ';
            }
            // validate category
            if ($_POST['category'] !== 0) {
                $category = $_POST['category'];
            } else {
                $errors = 'Please select a category';
            }
            // check for errors 
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            // if none then go ahead and update the member
            if (empty($errors)) {
                // update the user 
                $query = 'INSERT INTO items(Name,Description,Price,Country_Made,Status,Cat_ID,Member_ID) VALUES (?,?,?,?,?,?,?)';
                $stmt = $pdo->prepare($query);
                if ($stmt->execute([$name, $description, $price, $country, $status, $category, $member])) {
                    // echo success message
                    $msg =   $stmt->rowCount() . ' item has been Added !';
                    redirectHome($msg, 'back', 3, 'alert alert-success');
                } else {
                    $err = 'something went wrong: ' . $stmt->errorInfo();
                    redirectHome($err, 'back', 3, 'alert alert-danger');
                }
            }
        } else {
            $err = 'You cannot browse this page directly';
            redirectHome($err, 'back', 3, 'alert alert-warning');
        }
    } else if ($do == 'edit') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;
        $stmt = $pdo->prepare('SELECT * FROM items WHERE Item_ID = ?');
        $stmt->execute([$itemId]);
        $items = $stmt->fetch();
        // if the exist then the display the from...display error instead if the id doesn't exist
        if ($stmt->rowCount() > 0) { ?>
            <div class="ml-auto mr-auto container w-50 ">
                <h1 class="h1 text-center text-capitalize p-3">edit item</h1>
                <form class="form" method="post" action="?do=update">
                    <input type="hidden" name="itemId" value="<?php echo $itemId ?>">
                    <div class="form-group ">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" required="required" placeholder="the name of the item" value="<?php echo $items['Name'] ?>">
                    </div>
                    <div class="form-group ">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" required="required" placeholder="Describe the item"><?php echo $items['Description'] ?></textarea>
                    </div>
                    <div class="form-group ">
                        <label for="price">Price</label>
                        <input type="text" name="price" class="form-control" required="required" placeholder="the price of the item " value="<?php echo $items['Price'] ?>">
                    </div>
                    <div class="form-group ">
                        <label for="price">Country</label>
                        <input type="text" name="country" class="form-control" required="required" placeholder="which country the item were made ?" value="<?php echo $items['Country_Made'] ?>">
                    </div>
                    <div class="form-group ">
                        <label for="price">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" <?php if ($items["Status"] == 1) {
                                                    echo "selected";
                                                } ?>>Brand New</option>
                            <option value="2" <?php if ($items["Status"] == 2) {
                                                    echo "selected";
                                                } ?>>Like New</option>
                            <option value="3" <?php if ($items["Status"] == 3) {
                                                    echo "selected";
                                                } ?>>Old</option>
                            <option value="4" <?php if ($items["Status"] == 4) {
                                                    echo "selected";
                                                } ?>>Very Old</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="member">Member</label>
                        <select name="member" class="form-control">
                            <?php
                            $stmt = $pdo->prepare('SELECT * FROM users');
                            $stmt->execute();
                            $users = $stmt->fetchAll();
                            foreach ($users as $user) {
                                echo "<option value ='" . $user['UserID'] . "'";
                                if ($items['Member_ID'] == $user['UserID']) {
                                    echo 'selected';
                                }
                                echo ">" . $user['Username'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" class="form-control">
                            <?php
                            $stmt = $pdo->prepare('SELECT * FROM categories');
                            $stmt->execute();
                            $cats = $stmt->fetchAll();
                            foreach ($cats as $cat) {
                                echo "<option value='" . $cat['ID'] . "'";
                                if ($items['Cat_ID'] == $cat['ID']) {
                                    echo "selected";
                                }
                                echo ">" . $cat['Name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info px-5">Update item</button>
                    </div>
                </form>
            </div>
            <!-- COMMENT SECTION ABOUT SPECIFIC ITEM -->
            <?php //manage page
            $stmt = $pdo->prepare("SELECT comments.*, users.Username AS USERNAME
                                    FROM comments
                                    JOIN users
                                    ON users.UserID = comments.user_id
                                    WHERE item_id = ?");
            $stmt->execute([$itemId]);
            //save result as array to var
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($comments)) {
            ?>
                <h1 class="h1 text-center mr-auto ml-auto mt-5 mb-2">manage "<?= $items['Name'] ?>" comments</h1>
                <div class="container">
                    <table class="table table-sm table-striped table-bordered main-table">
                        <thead>
                            <tr>
                                <td>Comment</td>
                                <td>Comment Date</td>
                                <td>User</td>
                                <td>Operation</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comments as $comment) : ?>
                                <tr>
                                    <td><?= $comment['comment'] ?></td>
                                    <td><?= $comment['comment_date'] ?></td>
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
<?php      }
        } else {
            // which means that the id doesn't exist 
            $err = "item with that id doesn't exist !";
            redirectHome($err, 'back', 4);
        }
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //hold the user id from hidden input 
            $itemId = $_POST['itemId'];
            // store all errors through an array
            $errors = array();
            //validate item's name 
            if (empty($_POST['name'])) {
                $errors[] = 'item name is required !';
            } else {
                $name = filterString($_POST['name']);
                if (!$name) {
                    $errors[] = 'your username is not valid !';
                }
            }
            // validate description
            if (empty($_POST['description'])) {
                $errors[] = 'description is required !';
            } else {
                $desc = filterString($_POST['description']);
                if (!$desc) {
                    $errors[] = 'description is not valid !';
                }
            }
            //validate price
            if (empty($_POST['price'])) {
                $errors[] = 'price is required !';
            } else {
                $price = filterNumber($_POST['price']);
                if (!$name) {
                    $errors[] = 'price is not valid !';
                }
            }
            // validate Country
            if (empty($_POST['country'])) {
                $errors[] = 'country is required !';
            } else {
                $country = filterString($_POST['description']);
                if (!$country) {
                    $errors[] = 'country is not valid !';
                }
            }
            $status = $_POST['status'];
            $member = $_POST['member'];
            $category = $_POST['category'];

            // check for errors 
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            // if none then go ahead and update the member
            if (empty($errors)) {
                // update the user 
                $query = "UPDATE items
                    SET 
                    Name = ?,
                    Description = ?,
                    Price = ?,
                        Country_Made = ?,
                        Status = ?,
                        Cat_ID = ?,
                        Member_ID = ?
                  WHERE Item_ID = ?";
                $stmt = $pdo->prepare($query);
                if ($stmt->execute([$name, $desc, $price, $country, $status, $category, $member, $itemId])) {
                    // echo success message
                    $msg =  $stmt->rowCount() . ' has been updated';
                    redirectHome($msg, 'back', 3, 'alert alert-success');
                } else {
                    $err = 'something went wrong: ' . $pdo->errorInfo();
                    redirectHome($err, 'back', 3, 'alert alert-danger');
                }
            }
        } else {
            $err = 'You cannot browse this page directly';
            redirectHome($err, 'back', 3, 'alert alert-warning');
        }
    } else if ($do == 'approve') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;
        // check if the user exist based on userId
        $check = checkItem('Item_ID', 'items', $itemId);
        if ($check > 0) {
            // then Approve the item 
            $stmt = $pdo->prepare('UPDATE items SET Approve = 1  WHERE Item_ID = ?');
            if ($stmt->execute([$itemId])) {
                $msg = ' record has been Approved successfully! ';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error accurred while Approving,please try again : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-info');
            }
        } else {
            $error = 'item with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    } else if ($do == 'delete') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;
        // check if the item exist based on item id
        $check = checkItem('Item_ID', 'items', $itemId);
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('DELETE FROM items WHERE Item_ID = ?');
            if ($stmt->execute([$itemId])) {
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
