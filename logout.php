<?php
session_start();
session_unset();
session_destroy();
//after logout we must redicerct to index 
header('Location: index.php');
exit;
