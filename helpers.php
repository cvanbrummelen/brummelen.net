<?php

function get_db_version($db) {
	$found = 0;
	$version = 0;
	$tables = mysqli_list_tables($db);
	if (mysqli_num_rows($tables) == 0)
		return 0;

	while ($row = mysqli_fetch_array($tables)) {
		if ($row[0] == "variables")
			$found = 1;
	}
	mysqli_free_result($tables);
	if (!$found)
		return 1;

	$result = Query($db, "SELECT value FROM variables WHERE name='version'");
	if (mysqli_num_rows($result) != 1)
		return -1;

	$row = mysqli_fetch_array($result);
	return $row["value"];
}

function get_db_var($db, $name) {
	$result = Query($db, "SELECT value FROM variables WHERE name='$name'");
	if (mysqli_num_rows($result) != 1)
		die("Variable $name not found.\n");

	$row = mysqli_fetch_array($result);
	return $row["value"];
}

function get_input_var($name, $default = "") {
	$var = "";

	if (isset($_POST[$name]) && strlen($_POST[$name]))
		$var = $_POST[$name];
	else if (isset($_GET[$name]) && strlen($_GET[$name]))
		$var = $_GET[$name];
	else
		$var = $default;
	
	return $var;
}

function get_query_post_var($name) {
	global $link;
	$var = "";

	if (isset($_POST[$name]) && strlen($_POST[$name]))
		$var = mysqli_escape_string($link, $_POST[$name]);
	
	return $var;
}

function get_query_get_var($name) {
	global $link;	
	$var = "";

	if (isset($_GET[$name]) && strlen($_GET[$name]))
		$var = mysqli_escape_string($link, $_GET[$name]);
	
	return $var;
}

function createLink($overtext,$text,$link,$onclick) {
	$total = "<A target=\"_blank\" HREF=\"".$link."\"";
	if($text == "P")
	{
		$text = "";
	}
	if (strlen($onclick))
		$total .= " ";

	$total .= ">$text</A>\n";

	return $total;
}

?>
