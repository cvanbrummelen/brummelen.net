<?php
global $logged_on, $user_id, $PHP_SELF;

if (!$logged_on)
	die("Not logged in.\n");

$query = 0;
$str = "Add";
$c_id = "";
$c_name = "";
$c_parent = "";
$mode = "add";
$category = "";

if (strlen($_GET["mode"])) {
	if ($_GET["mode"] == "del" && strlen($_GET["category"])) {
		$mode = "del";
		$str = "Delete";
		$query = 1;
		$category = get_query_get_var("category");
	}
	if ($_GET["mode"] == "edit" && strlen($_GET["category"])) {
		$mode = "edit";
		$str = "Edit";
		$query = 1;
		$category = get_query_get_var("category");
	}
}
    
if ($query) {
	$result = Query($DBDatabase,
		"SELECT id, name, parent FROM categories WHERE id='$category' AND user='$user_id'");

	if ($row = mysqli_fetch_array($result)) {
		$c_id = $row["id"];
		$c_name = $row["name"];
		$c_parent = $row["parent"];
		mysqli_free_result($result);
	}
}

echo "<H2>".$str." category</H2>\n";

if ($mode == "del") {
	echo "<FONT COLOR=Red>Warning: All links in this category will be removed ".
		"also.</FONT><BR>\n";
}
	
echo "<FORM METHOD=\"POST\" ACTION=\"$PHP_SELF?show=main\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"submit".$mode."category\">\n";
echo "<INPUT TYPE=\"hidden\" NAME=\"c_id\" VALUE=\"".$c_id."\">\n";
echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\">\n";

// READONLY als DELETE
echo "<TR><TD><B>Name</B></TD><TD><INPUT TYPE=\"text\" SIZE=\"40\" NAME=\"c_name\" VALUE=\"".$c_name."\"></TD></TR>\n";
echo "<TR><TD><B>Parent</B></TD><TD>\n";
$result = Query($DBDatabase,"SELECT id,name FROM categories WHERE id!='$c_id' AND id!=1 AND user='$user_id'");

// DISABLED als DELETE
echo "<SELECT name=\"c_parent\">\n";
echo "<option value=\"1\">No Parent\n";
while ($row = mysqli_fetch_array($result)) {
	echo "<OPTION VALUE=\"".$row["id"]."\"".
		((strlen($c_parent)&&$c_parent==$row["id"])?" SELECTED":"").">".$row["name"]."\n";
}

mysqli_free_result($result);
echo "</SELECT>\n";
echo "</TD></TR>\n";
echo "</TABLE>\n";
echo "<INPUT TYPE=\"submit\" value=\"$str\">\n";
echo "</FORM>\n";
?>
