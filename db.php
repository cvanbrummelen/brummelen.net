<?php
include_once("config.php");


$link = null;

function Connect() {
  global $DBServer,$DBUser,$DBPwd, $link;
  $link = mysqli_connect($DBServer,$DBUser,$DBPwd)
    or die("Cannot connect to MySQL server.");
  return $link;
}

function Query($DB,$Query, $die = true) {
	Connect();
global $link;  
mysqli_select_db($link, "brummelen_net");   
if (!$die)
{
    $result = mysqli_query($link,$Query);
}
  else
  	{
    $result = mysqli_query($link,$Query)
    or die("Query failed: ".mysqli_errno($link).": ".mysqli_error($link));
	}
  return $result;
}
?>
