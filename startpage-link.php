<?php
global $logged_on, $user_id, $PHP_SELF;

if (!$logged_on)
	die("Not logged in.\n");

$query = 0;
$str = "Add";
$c_id = "";
$c_name = "";
$c_link = "";
$c_category = "";
$mode = "add";
$link = "";

if (strlen($_GET["mode"])) {
	if ($_GET["mode"] == "add" && strlen($_GET["category"])) {
		$c_category = $_GET["category"];
	} else if ($_GET["mode"] == "del" && strlen($_GET["link"])) {
		$mode = "del";
		$link = get_query_get_var("link");
		$str = "Delete";
		$query = 1;
	} else if ($_GET["mode"] == "edit" && strlen($_GET["link"])) {
		$mode = "edit";
		$link = get_query_get_var("link");
		$str = "Edit";
		$query = 1;
	}
}
    
if ($query) {
  $result = Query($DBDatabase,"SELECT id,name,link,category FROM links WHERE ".
  	"id='$link' AND user='$user_id'");
  if ($row = mysqli_fetch_array($result)) {
    $c_id = $row["id"];
    $c_name = $row["name"];
    $c_link = $row["link"];
    $c_category = $row["category"];
    mysql_free_result($result);
  }
}

echo "<H2>".$str." link!</H2>\n";
echo "<FORM METHOD=\"POST\" ACTION=\"$PHP_SELF?show=main\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"submit".$mode."link\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"c_id\" VALUE=\"".$c_id."\">\n";
echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\">\n";
// READONLY als DELETE
echo "<TR><TD><B>Name</B></TD><TD><INPUT TYPE=\"text\" SIZE=\"40\" ".
	"NAME=\"c_name\" VALUE=\"".$c_name."\"></TD></TR>\n";
echo "<TR><TD><B>Link</B></TD><TD><INPUT TYPE=\"text\" SIZE=\"60\" ".
	"NAME=\"c_link\" VALUE=\"".$c_link."\"></TD></TR>\n";
echo "<TR><TD><B>Category</B></TD><TD>\n";
$result = Query($DBDatabase,"SELECT id,name FROM categories WHERE id!='1' AND user='$user_id'");
// DISABLED als DELETE
echo "<SELECT name=\"c_category\">\n";
while ($row = mysqli_fetch_array($result)) {
  echo "<OPTION VALUE=\"".$row["id"]."\"".
  	((strlen($c_category) && $c_category == $row["id"])?" SELECTED":"").
	">".$row["name"]."\n";
}
//mysql_free_result($result);
echo "</SELECT>\n";
echo "</TD></TR>\n";
echo "</TABLE>\n";
echo "<INPUT TYPE=\"submit\" value=\"$str\">\n";
echo "</FORM>\n";

?>
