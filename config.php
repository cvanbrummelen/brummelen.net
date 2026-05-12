<?php
// Startpage configuration file
//

// Database Configuration
//
$DBServer		= 'localhost';
$DBUser			= 'brummelen_net';
$DBPwd			= '64mxbw6EiPuf0QJ';
$DBDatabase		= 'brummelen_net';

$hostadres = "localhost";   
$gebruikersnaam = "brummelen_net";
$password = "64mxbw6EiPuf0QJ";
$database = "brummelen_net";
if(!($link_id = mysqli_connect($hostadres, $gebruikersnaam, $password))) die(mysqli_connect_error());   
mysqli_select_db($link_id, $database);   

// HTML Configuration
// The HTML look/layout of the startpage
//
$HTMLTitle		= 'Brummelen.net';
$HTMLDescription	= 'Description';
$HTMLKeywords		= 'Keywords';
$HTMLStyleSheet		= 'startpage.css';
$HTMLHeader		= '';

// Layout Configuration
//

// The default number of columns to display.
//
$DefaultColumnCount	= 4;

// Should startpage combine Child categories with their parent categories. This is done visually
// and link counts also count for their own and parent categories.
//
$CombineCategoryChilds  = 1;

?>
