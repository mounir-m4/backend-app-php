<?php
// connect to database
function pdoConnectMysql()
{
    $DB_HOST  = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = 'Mounir@1995';
    $DB_NAME = "shop";
    try {
        return new PDO('mysql:host=' . $DB_HOST . ';dbname=' . $DB_NAME . ';charset=utf8', $DB_USER, $DB_PASS);
    } catch (PDOException $ex) {
        // If there is an error with the connection, stop the script and display the error.
        exit('Error accured while accessing to database: ' . $ex->getMessage());
    }
}
$pdo  = pdoConnectMysql();
