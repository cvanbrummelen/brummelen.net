<?php
global $logged_on, $user_id, $PHP_SELF;

if (!$logged_on)
	die("Not logged in.\n");

if ($_SESSION['admin'] != 1)
	die("No admin rights.\n");

$mode = get_input_var("mode");
$query = 0;
$form = 0;
$c_id = -1;
$c_username = "";
$c_password1 = "";
$c_password2 = "";

if (!strlen($mode))
	die("No mode selected.\n");

if ($mode == "list") {
  $result = Query($DBDatabase, "SELECT id, username FROM users");
  
  echo "<h2>Users</h2>\n";
  echo "<ul>\n";
  while ($row = mysqli_fetch_array($result)) {
	echo "<li>".$row["username"]."&nbsp;<a href=\"$PHP_SELF?show=users&mode=del&id=".$row["id"]."\">del</a></li>\n";
  }
  echo "</ul>\n";
  mysqli_free_result($result);

  echo createLink("Add user", "Add user", "$PHP_SELF?show=users&mode=add", "");
} else if ($mode == "add") {
	$mode = "add";
	$form = 1;
	$str = "Add";
} else if ($mode == "del" && strlen($_GET["id"])) {
	$mode = "del";
	$user = get_query_get_var("id");
	$str = "Delete";
	$query = 1;
	$form = 1;
}
    
if ($query) {
  $result = Query($DBDatabase, "SELECT id, username FROM users WHERE ".
  	"id = $user");
  if ($row = mysqli_fetch_array($result)) {
    $c_id = $row["id"];
    $c_username = $row["username"];
    mysqli_free_result($result);
  }
}

if ($form) {
	echo "<h2>".$str." user</h2>\n";
	echo "<form method=\"POST\" action=\"$PHP_SELF?show=users\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"submit".$mode."user\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"list\">\n";
	echo "<input type=\"hidden\" name=\"c_id\" value=\"".$c_id."\">\n";
	echo "<table border=\"0\" cellspacing=\"0\">\n";
	// READONLY als DELETE
	echo "<tr><td><b>Name</b></td><td><input type=\"text\" size=\"40\" ".
		"name=\"c_username\" value=\"".$c_username."\"></td></tr>\n";
	echo "<tr><td><b>Password</b></td><td><input type=\"password\" size=\"40\" ".
		"name=\"c_password1\" value=\"".$c_password1."\"></td></tr>\n";
	echo "<tr><td><b>Verification</b></td><td><input type=\"password\" size=\"40\" ".
		"name=\"c_password2\" value=\"".$c_password2."\"></td></tr>\n";
	echo "</table>\n";
	echo "<input type=\"submit\" value=\"$str\">\n";
	echo "</form>\n";
}

echo createLink("Log off","Log off","$PHP_SELF?action=submitlogoff", "");
echo createLink("Main", "Main", "$PHP_SELF?show=main", "");

?>
