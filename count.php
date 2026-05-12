<?php
require("db.php");
require("helpers.php");

echo "<HTML><HEAD></HEAD><BODY onLoad=\"window.close()\">\n";

$id = get_query_get_var("id");
$category = get_query_get_var("category");

echo "Counting link $id<BR>\n";

function count_category($category) {
	global $DBDatabase, $CombineCategoryChilds;

	$result = Query($DBDatabase, "SELECT parent, count FROM categories ".
		"WHERE id='$category'");
	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result);
		echo "Count is now: ".$row["count"]."<BR>\n";
		if (is_numeric($row["count"])) {
			$newcount = $row["count"] + 1;
			Query($DBDatabase, "UPDATE categories SET count=\"$newcount\" WHERE ".
				"id=\"$category\"");
		}

		if ($CombineCategoryChilds && is_numeric($row["parent"])) {
			if ($row["parent"] != 1)
				count_category($row["parent"]);
		}
	}
}

if (!is_numeric($id) || !is_numeric($category)) {
	echo "Not a number.<BR>\n";
} else {
	Connect();
	$result = Query($DBDatabase, "SELECT count FROM links WHERE id='$id'");
	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result);
		echo "Count is now: ".$row["count"]."<BR>\n";
		if (is_numeric($row["count"])) {
			$newcount = $row["count"] + 1;
			Query($DBDatabase,
				"UPDATE links SET count='$newcount' WHERE ".
				"id='$id'");
		}
	}
	count_category($category);
}

echo "</BODY></HTML>\n";
?>
