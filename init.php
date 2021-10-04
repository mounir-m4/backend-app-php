<?php
//routes
$lang = 'includes/languages/';
$template = 'includes/templates/';
$func = 'includes/functions/';
$css = 'layout/css/';
$js = 'layout/js/';

//includes 
include $lang . 'eng.php';
include 'connect.php';
include $func . 'functions.php';
include $template . 'header.php';

// condition to control which pages must contain navbar
if (!isset($noNavbar)) {
    include $template . 'nav.php';
}
