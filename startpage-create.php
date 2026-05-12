<html>
<head>
<title>Startpage init</title>
</head>
<body>
<?php
include ("db.php");

$Categories_table = "CREATE TABLE categories (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, parent INT NOT NULL REFERENCES categories, user INT NOT NULL REFERENCES users, count INT NOT NULL DEFAULT 0, PRIMARY KEY(id));";
$Links_table = "CREATE TABLE links (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, category INT NOT NULL REFERENCES categories, user INT NOT NULL REFERENCES users, count INT NOT NULL DEFAULT 0, PRIMARY KEY(id));";
$Users_table = "CREATE TABLE users (id INT NOT NULL AUTO_INCREMENT, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id), UNIQUE(username));";
$Variables_table = "CREATE TABLE variables (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id), UNIQUE(name));";
$Categories_name = "categories";
$Links_name = "links";
$Users_name = "users";
$Variables_name = "variables";

$tablelist = array ("Variables", "Users", "Categories", "Links");

Connect();
function get_db_version($db) {
	global $link;
	$found = 0;
	$version = 0;
	$tables = mysqli_query($link, "SHOW TABLES");
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

echo "<H1>Startpage init</H1>";

# Check for version of the database
#
echo "<h2>Checking database version</h2>";
$version = get_db_version($DBDatabase);

echo "Database version: $version<br/>\n";

function AddCategory($name, $parent, $user) {
	global $DBDatabase, $link;
	if (Query($DBDatabase,"INSERT INTO categories (name, parent, user) values ('$name', '$parent', '$user')"))
		echo "Entry added to categories.<BR>";
	else
		echo "Failed to add category: ".mysqli_error($link)."<br/>";
	return mysqli_insert_id($link);
}

function AddLink($name, $link_url, $category, $user) {
	global $DBDatabase, $link;
	if (Query($DBDatabase,"INSERT INTO links (name, link, category, user) values ('$name', '$link_url', '$category', '$user')"))
		echo "Entry added to links.<BR>";
	else
		echo "Failed to add link: ".mysqli_error($link)."<br/>";
	return mysqli_insert_id($link);
}

function AddUser($username, $password) {
	global $DBDatabase, $link;
	if (Query($DBDatabase, "INSERT INTO users (username, password) VALUES ('$username', MD5('$password'));"))
		echo "Entry added to users.<br/>\n";
	else
		echo "Failed to add user: ".mysqli_error($link)."<br/>\n";
	return mysqli_insert_id($link);
}

function AddVariable($name, $value) {
	global $DBDatabase, $link;
	if (Query($DBDatabase, "INSERT INTO variables (name, value) VALUES ('$name', '$value')"))
		echo "Entry added to variables.<br/>\n";
	else
		echo "Failed to add variable: ".mysqli_error($link)."<br/>\n";
	return mysqli_insert_id($link);
}

function CreateTable($table) {
	global $DBDatabase, $link;

	$tablestr=$table."_table";
	global $$tablestr;

	if (Query($DBDatabase, $$tablestr)) {
		echo "Table ".$table." created successfully.<BR>";
	} else {
		echo "Error creating table: ".mysqli_error($link)."<BR>";
	}
}

if ($version == 0) {
	global $PHP_SELF;
	echo "<H2>Creating new tables</H2>";
	$c_username = "";
	$c_password = "";
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$c_username = mysqli_real_escape_string($link, $_POST["username"]);
		$c_password = mysqli_real_escape_string($link, $_POST["password"]);
	}
	
	if (!strlen($c_username) || !strlen($c_password)) {
		echo "First user information is required.\n";
		echo "<form method=post action=\"".$PHP_SELF."\">\n";
		echo "Name: <input type=text name=username value=\"$c_username\"><br/>\n";
		echo "Password: <input type=password name=password value=\"$c_password\"><br/>\n";
		echo "<input type=submit>\n";
		exit(0);
	}

	reset($tablelist);
	while($table=current($tablelist)) {
		CreateTable($table);
		next($tablelist);
	}

	if (Query($DBDatabase,"INSERT INTO categories (name,parent,user) values (\"No parent\",\"1\",'0')")) {
		echo "Entry added to categories.<BR>";
	} else {
		echo "Error adding entry: ".mysqli_error($link)."<BR>";
	}

	echo "Creating initial fill.<br/>\n";
	AddUser($c_username, $c_password);
	AddVariable('version', '2');
	AddVariable('default_user', $c_username);
	AddVariable('admin_user', $c_username);

	$News = AddCategory("News", 1, 1);
	$Search = AddCategory("Search", 1, 1);

	AddLink("Slashdot", "http://www.slashdot.org", $News, 1);
	AddLink("The Register", "http://www.theregister.co.uk", $News, 1);
	AddLink("Google", "http://www.google.com", $Search, 1);
}

if ($version == 1) {
	global $PHP_SELF;
	echo "Upgrading to version 2<br/>\n";
	$c_username = "";
	$c_password = "";
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$c_username = mysqli_real_escape_string($link, $_POST["username"]);
		$c_password = mysqli_real_escape_string($link, $_POST["password"]);
	}
	
	if (!strlen($c_username) || !strlen($c_password)) {
		echo "First user information is required.\n";
		echo "<form method=post action=\"".$PHP_SELF."\">\n";
		echo "Name: <input type=text name=username value=\"$c_username\"><br/>\n";
		echo "Password: <input type=password name=password value=\"$c_password\"><br/>\n";
		echo "<input type=submit>\n";
		exit(0);
	}
	if (Query($DBDatabase, "ALTER TABLE categories ADD user INT NOT NULL REFERENCES users AFTER parent;")) {
		echo "Table categories altered.<br/>\n";
	} else {
		echo "Failed to alter table categories: ".mysqli_error($link)."<br/>\n";
	}
	if (Query($DBDatabase, "UPDATE categories SET user=1 WHERE id!=1;")) {
		echo "Table categories updated.<br/>\n";
	} else {
		echo "Failed to update table categories: ".mysqli_error($link)."<br/>\n";
	}
	if (Query($DBDatabase, "ALTER TABLE links ADD user INT NOT NULL REFERENCES users AFTER category;")) {
		echo "Table links altered.<br/>\n";
	} else {
		echo "Failed to alter table links: ".mysqli_error($link)."<br/>\n";
	}
	if (Query($DBDatabase, "UPDATE links SET user=1;")) {
		echo "Table links updated.<br/>\n";
	} else {
		echo "Failed to update table links: ".mysqli_error($link)."<br/>\n";
	}
	CreateTable("Users");
	CreateTable("Variables");
	AddUser($c_username, $c_password);
	AddVariable('version', '2');
	AddVariable('default_user', $c_username);
	AddVariable('admin_user', $c_username);
}

if ($version == 2) {
	echo "Database is up-to-date.<br/>\n";
	if (strlen($_POST["Flush"])) {
		echo "Flushing/Deleting old database.<br/>\n";
		echo "<H2>Dropping old tables</H2>";
		reset($tablelist);
		while($table=current($tablelist)) {
			$tablestr=$table."_name";
			$tablestr2 = "DROP TABLE ".$$tablestr.";";
			if (Query($DBDatabase, $tablestr2, false)) {
				echo "Table ".$table." dropped successfully.<BR>";
			} else {
				echo "Error dropping table: ".mysqli_error($link)."<BR>";
			}
			next($tablelist);
		}
	}
}
exit(1);

?>
</body>
</html>
