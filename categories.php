<?php
// Welcome to members page 
// here you can add delete update a user 
session_start();
$pageTitle = "Categories";
if (isset($_SESSION['Username'])) {
    // Include The Important Files
    include 'init.php';
    $pdo = pdoConnectMysql();
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if ($do == "manage") {
        // load all categories
        // dynamic Sorting code
        $sort = 'ASC';
        $sorting = ['ASC', 'DESC'];
        if (isset($_GET['sort']) && in_array($_GET['sort'], $sorting)) {
            $sort = $_GET['sort'];
        }
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY Ordering $sort");
        $stmt->execute();
        $cats =  $stmt->fetchAll();
        if (!empty($cats)) {
?>
            <h1 class="display-4 text-center">Manage Categories</h1>
            <div class="container mt-3 categories w-75">
                <a href="categories.php?do=add" class="btn btn-primary text-capitalize btn-md mb-2"><i class="fa fa-plus"></i> add new category</a>
                <div class="card">
                    <div class="card-header">
                        <span class="h4">Manage categories</span>
                        <div class="ordering float-right d-flex">
                            <span class="font-weight-light mr-2 mt-2">
                                <i class="fa fa-sort font-weight-bold mr-1"></i>
                                Ordering:</span>
                            <div class="d-inline order-style">
                                <a href="?sort=ASC" class="sorting <?php echo $sort == 'ASC' ? 'active' : '' ?>">Asc</a> |
                                <a href="?sort=DESC" class="sorting <?php echo $sort == 'DESC' ? 'active' : '' ?>">Desc</a>
                            </div>
                            <span class="font-weight-light mr-2 ml-3 mt-2">
                                <i class="fa fa-eye font-weight-bold mr-1"></i>
                                View:</span>
                            <div class="d-inline order-style view">
                                <span class="sorting active" data-view="full">Full</span> |
                                <span class="sorting" data-view="classic">Classic</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        foreach ($cats as $cat) {
                            $desc = $cat['Description'] == '' ?  'this category has no description'  :  $cat['Description'];
                            $visible = $cat['Visibility'] == 1 ? '<span id="visibility"><i class="fa fa-eye mr-1"></i>Hidden</span>' : '';
                            $comments = $cat['Allow_Comment'] == 1 ? '<span id="comment"><i class="fa fa-comment mr-1"></i>Comments disabled</span>' : '';
                            $ads = $cat['Allow_Ads'] == 1 ? '<span id="ads"><i class="fas fa-ad"></i>Ads disabled</span>' : '';
                            echo '<div class="cat">';
                            echo '<div class="hidden-buttons">';
                            echo '<a href="categories.php?do=edit&catid=' . $cat["ID"] . '" class="btn btn-info btn-sm "><i class="fa fa-edit mr-1"></i>Edit</a>';
                            echo "<a href='categories.php?do=delete&catid=" . $cat['ID'] . " ' class='btn btn-danger btn-sm '><i class='fa fa-trash mr-1'></i>Delete</a>";
                            echo "</div>";
                            echo '<h3 class="h3">' . $cat['Name'] . '</h3>';
                            echo '<div class="full-view">';
                            echo  '<p>' . $desc . '</p>';
                            echo $visible;
                            echo $comments;
                            echo $ads;
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php  } else {
            echo "<div class='mt-4 container ml-auto mr-auto text-center w-50 alert alert-info'>
            <p class='lead p-3'>there is no record to be shown !</p>
            <a href='categories.php?do=add' class='btn btn-primary text-capitalize btn-md mb-2'>
            <i class='fa fa-plus'></i>
             add new category</a>
            </div>";
        }
    } else if ($do == 'add') { ?>
        <div class="ml-auto mr-auto container w-50 ">
            <h1 class="h1 text-center text-capitalize p-3">add new category</h1>
            <form class="form" method="post" action="?do=insert">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" required="required" placeholder="the category name">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" placeholder="describe the category"></textarea>
                </div>
                <div class="form-group">
                    <label for="ordering">Ordering</label>
                    <input type="text" name="ordering" class="form-control" placeholder="number to arrange the category">
                </div>
                <div class="form-group">
                    <div class="d-flex my-2">
                        <div class="flex-column w-25">
                            <label>Allow Visibility ?</label>
                        </div>
                        <div class="flex-column ml-3">
                            <input type="radio" name="visibility" id="vis-yes" value="0" checked>
                            <label for="vis-yes" class="mr-2">Yes</label>
                            <input type="radio" name="visibility" id="vis-no" value="1">
                            <label for="vis-no">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex my-2">
                        <div class="flex-column w-25">
                            <label>Allow Comments ?</label>
                        </div>
                        <div class="flex-column ml-3">
                            <input type="radio" name="comments" id="comments-yes" value="0" checked>
                            <label for="comments-yes" class="mr-2">Yes</label>
                            <input type="radio" name="comments" id="comments-no" value="1">
                            <label for="comments-no">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex my-2">
                        <div class="flex-column w-25">
                            <label>Allow Ads ?</label>
                        </div>
                        <div class="flex-column ml-3">
                            <input type="radio" name="ads" id="ads-yes" value="0" checked>
                            <label for="comments-yes" class="mr-2">Yes</label>
                            <input type="radio" name="ads" id="ads-no" value="1">
                            <label for="comments-no">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-block">Add Category</button>
                </div>

            </form>
        </div>
        <?php
    } else if ($do == 'insert') { // insert a new category
        echo '<h1 class="h1 text-center text-capitalize p-3">Add category</h1>';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // store all errors through an array
            $errors = array();
            //validate username only because it's required
            if (empty($_POST['name'])) {
                $errors[] = 'category name is required !';
            } else {
                $name = filterString($_POST['name']);
                if (!$name) {
                    $errors[] = 'your category name is not valid !';
                }
            }
            // validate description
            $description = $_POST['description'];
            //validate ordering
            $ordering = $_POST['ordering'];
            //check for visibility
            $visible = $_POST['visibility'];
            // for comments
            $comment = $_POST['comments'];
            $ads = $_POST['ads'];
            // check for errors 
            foreach ($errors as $error) {
                redirectHome('fix those errors below', 'back', 4);
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            // if none then go ahead and update the member
            if (empty($errors)) {
                // check if the username is already exist or not
                $exist = checkItem('Name', 'categories', $name);
                if ($exist == 1) {
                    redirectHome('category is already exist !', 'back', 4);
                } else {
                    // update the user 
                    $query = 'INSERT INTO categories(Name,Description,Ordering,Visibility,Allow_Comment,Allow_Ads) VALUES (?,?,?,?,?,?)';
                    $stmt = $pdo->prepare($query);
                    if ($stmt->execute([$name, $description, $ordering, $visible, $comment, $ads])) {
                        // echo success message
                        $msg =   $stmt->rowCount() . ' category has been Added !';
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
        $catId = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE ID = ?');
        $stmt->execute([$catId]);
        $row = $stmt->fetch();
        // if the category exist then the display the from...display error instead if the id doesn't exist
        if ($stmt->rowCount() > 0) { ?>
            <div class="ml-auto mr-auto container w-50 ">
                <h1 class="h1 text-center text-capitalize p-3">Edit category</h1>
                <form class="form" method="post" action="?do=update">
                    <!-- hidden input for update is the record on the db -->
                    <input type="hidden" name="catid" value="<?php echo $catId ?>">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" required="required" placeholder="the category name" value="<?php echo $row['Name'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" placeholder="describe the category"><?php echo $row['Description'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="ordering">Ordering</label>
                        <input type="text" name="ordering" class="form-control" placeholder="number to arrange the category" value="<?php echo $row['Ordering'] ?>">
                    </div>
                    <div class="form-group">
                        <div class="d-flex my-2">
                            <div class="flex-column w-25">
                                <label>Allow Visibility ?</label>
                            </div>
                            <div class="flex-column ml-3">
                                <input type="radio" name="visibility" id="vis-yes" value="0" <?php if ($row['Visibility'] == 0) {
                                                                                                    echo 'checked';
                                                                                                }  ?>>
                                <label for="vis-yes" class="mr-2">Yes</label>
                                <input type="radio" name="visibility" id="vis-no" value="1" <?php if ($row['Visibility'] == 1) {
                                                                                                echo 'checked';
                                                                                            }  ?>>
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex my-2">
                            <div class="flex-column w-25">
                                <label>Allow Comments ?</label>
                            </div>
                            <div class="flex-column ml-3">
                                <input type="radio" name="comments" id="comments-yes" value="0" <?php if ($row['Allow_Comment'] == 0) {
                                                                                                    echo 'checked';
                                                                                                }  ?>>
                                <label for="comments-yes" class="mr-2">Yes</label>
                                <input type="radio" name="comments" id="comments-no" value="1" <?php if ($row['Allow_Comment'] == 1) {
                                                                                                    echo 'checked';
                                                                                                }  ?>>
                                <label for="comments-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex my-2">
                            <div class="flex-column w-25">
                                <label>Allow Ads ?</label>
                            </div>
                            <div class="flex-column ml-3">
                                <input type="radio" name="ads" id="ads-yes" value="0" <?php if ($row['Allow_Ads'] == 0) {
                                                                                            echo 'checked';
                                                                                        }  ?>>
                                <label for="comments-yes" class="mr-2">Yes</label>
                                <input type="radio" name="ads" id="ads-no" value="1" <?php if ($row['Allow_Ads'] == 1) {
                                                                                            echo 'checked';
                                                                                        }  ?>>
                                <label for="comments-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info btn-block">update Category</button>
                    </div>

                </form>
            </div>
<?php
        } else {
            // which means that the id doesn't exist 
            $err = "Category with that id doesn't exist !";
            redirectHome($err, 'back', 4);
        }
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<h1 class="h1 text-center text-capitalize p-3">edit category</h1>';
            // hold the category id from the hidden input
            $Id = $_POST['catid'];
            // stores all possible errors in array
            $errors = array();
            //Validate category name
            if (empty($_POST['name'])) {
                $errors[] = 'Category name is required !';
            } else {
                $name = filterString($_POST['name']);
                if (!$name) {
                    $errors[] = 'category name is not valid !';
                }
            }
            // validate description
            $description = $_POST['description'];
            //validate ordering
            $ordering = $_POST['ordering'];
            //check for visibility
            $visible = $_POST['visibility'];
            // for comments
            $comment = $_POST['comments'];
            //for Ads
            $ads = $_POST['ads'];
            // check for errors 
            foreach ($errors as $error) {
                redirectHome('fix those errors below', 'back', 4);
                echo '<div class="alert alert-danger w-50 ml-auto mr-auto text-center"> ' . $error . '</div>';
            }
            //if none then update the member
            if (empty($errors)) {
                // check if the category name is already exist or not
                $exist = checkItem('Name', 'categories', $name);
                // update the user 
                $query = "UPDATE categories SET Name = ?,Description = ?,Ordering = ?,Visibility = ?,Allow_Comment = ?,Allow_Ads = ? WHERE ID = ?";
                $stmt = $pdo->prepare($query);
                if ($stmt->execute([$name, $description, $ordering, $visible, $comment, $ads, $Id])) {
                    // echo success message
                    $msg =   $stmt->rowCount() . ' category has been Updated !';
                    redirectHome($msg, 'back', 5, 'alert alert-success');
                } else {
                    $errors = 'something went wrong: ' . $stmt->errorInfo();
                    redirectHome($errors, 'back', 5, 'alert alert-danger');
                }
            }
        }
    } else if ($do == 'delete') {
        // make sure that the id is exist and it's numeric otherwise prtint 0 as default
        $catId = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        // check if the user exist based on userId
        $check = checkItem('ID', 'categories', $catId);
        echo $check;
        if ($check > 0) {
            // then delete the record
            $stmt = $pdo->prepare('DELETE FROM categories WHERE ID = ?');
            if ($stmt->execute([$catId])) {
                $msg = 'record has been deleted successfully !';
                redirectHome($msg, 'back', 3, 'alert alert-success');
            } else {
                $err = 'an error accurred while deleting : ' . $pdo->errorInfo();
                redirectHome($err, 'back', 4, 'alert alert-danger');
            }
        } else {
            $error = 'category with that id doesn\'t exist ';
            redirectHome($error, 'back', 3);
        }
    }

    include $template . 'footer.php';
} else {
    header('Location: index.php');
    exit;
}
