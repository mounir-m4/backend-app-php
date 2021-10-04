<?php
//function to validate a string
function filterString($field)
{
    //sanitize string
    $field = filter_var(trim($field), FILTER_SANITIZE_STRING);
    if (!empty($field)) {
        return $field;
    } else {
        return false;
    }
}
// fucntion to filter a number
function filterNumber($field)
{
    $field = filter_var(trim(floatval($field)));
    if (!empty($field)) {
        return $field;
    } else {
        return false;
    }
}
// function to filter user inputs
function filterName($field)
{
    //sanitaze username
    $field = filter_var(trim($field), FILTER_SANITIZE_STRING);
    //validate username
    if (preg_match("/^[a-zA-Z ]*$/", $field)) {
        return $field;
    } else {
        return false;
    }
}
// function to filter email
function filterEmail($field)
{
    //sanitaze email adresse
    $field = filter_var(trim($field), FILTER_SANITIZE_EMAIL);
    //validate email
    if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
        return $field;
    } else {
        return false;
    }
}
//function to validate a password
function filterPassword($field)
{
    $field = filter_var($field, FILTER_SANITIZE_STRING);
    if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $field)) {
        return $field;
    } else {
        return false;
    }
}
//function to echo page title dinamically
function getTitle()
{
    global $pageTitle;
    echo isset($pageTitle) && !empty($pageTitle) ? $pageTitle  : 'default';
}
/* function RedirectHome [Accept params]
$errorMsg = Echo the error msg
$to =   the page that you want to rediect to
$seconds = seconds before redirect
*/
function redirectHome($msg, $to = null, $seconds = 3, $class = "alert alert-danger")
{
    if ($to === null) {
        $to = 'index.php';
        $link = 'Homepage';
    } else {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '') {
            $to = $_SERVER['HTTP_REFERER'];
            $link = 'Previous page';
        } else {
            $to = 'index.php';
            $link = 'Homepage';
        }
    }
    echo '<div class=" ml-auto mr-auto text-center w-50 ' . $class . ' mt-3">' . $msg . '<br>
      you will be redirected to ' . $link . ' after ' . $seconds . ' Seconds</div>';
    header("refresh:$seconds;url=$to");
    exit;
}

/*
   ** check items function v1.0
   ** Function to check item in database [function accept params]
   ** $select = the item to select [Example: user,item,student]
   ** $from   = the table to select from [users,items,categories]
   ** $value  = the value or the condition  of select [Example: WHERE = blue,box...] 
*/
function checkItem($select, $from, $value)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT $select FROM $from WHERE $select = ?");
    $stmt->execute(array($value));
    return $stmt->rowCount();
}
/* 
** Count the number the items Function
** $column : the item to count
** $table  : the table to count from
*/
function countItems($column, $table)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT($column) FROM $table");
    $stmt->execute();
    return  $stmt->fetchColumn();
}
/*
**Function to retun the lastest item baed on conditions
** $select = the item to select
** $from = which table ?
**$order : which column u want to order it by DESC ?
**$limit : how many item should be selected ? default  = 5
*/
function getLatest($select, $from, $order, $limit = 5)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT $select FROM $from ORDER BY $order DESC LIMIT $limit");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return $rows;
}
