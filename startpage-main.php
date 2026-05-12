<?php
global $PHP_SELF;

function printHeader($id, $str) {
	global $logged_on, $user_id, $show_id, $HeaderBackground, $PHP_SELF;
	
	$text = "";
	
	$text .= "<TR CLASS=\"Header\">";
	$text .= "  <TD NOWRAP><B>$str</B></TD>";
	if ($logged_on && $user_id == $show_id) {
		$text .= "  <TD NOWRAP COLSPAN=2 ALIGN=\"right\">";
		$text .= createLink("Add a new link",
			"<IMG SRC=\"startpage_images/add.gif\" WIDTH=\"20\" BORDER=\"0\">",
			"$PHP_SELF?show=link&mode=add&category=$id","");
		$text .= createLink("Edit this category", "<IMG SRC=\"startpage_images/info.gif\" ".
			"WIDTH=\"10\" BORDER=\"0\">",
			"$PHP_SELF?show=category&mode=edit&category=$id", "");
		$text .= createLink("Delete this category",
			"<IMG SRC=\"startpage_images/trash.gif\" WIDTH=\"10\" BORDER=\"0\">",
			"$PHP_SELF?show=category&mode=del&category=$id", "");
		$text .= "&nbsp;</TD>";
	} else {
		$text .= "  <TD COLSPAN=3>&nbsp;</TD>";
	}
	$text .= "</TR>";

	return $text;
}

function printLink($id, $link, $linktext, $category) {
	global $logged_on, $user_id, $show_id, $LinkBackground, $PHP_SELF;

	$text = "";
	
	$text .= "<TR CLASS=\"Link\">";
	$text .= "<TD NOWRAP>";
	$text .= createLink($link, $linktext, $link,
		"javascript:window.open(\"count.php?id=$id&category=$category\", \"a\", ".
		"config=\"height=1,width=1,toolbar=no,menubar=no,scrollbars=no,resizable=no,".
		"location=no,directories=no,status=no\");document.location=\"$link\";void(\"\");");
	$text .= "</TD>";
	$text .= "<TD NOWRAP>&nbsp;";
	$text .= createLink($link, "P", $link,
		"javascript:window.open(\"count.php?id=$id&category=$category\", \"a\", ".
		"config=\"height=1,width=1,toolbar=no,menubar=no,scrollbars=no,resizable=no,".
		"location=no,directories=no,status=no\");window.open(\"$link\");return false;");
	$text .= "</TD>";
	if ($logged_on && $user_id == $show_id) {
		$text .= "  <TD ALIGN=\"right\" NOWRAP>";
		$text .= createLink("Edit this link", "<IMG SRC=\"startpage_images/info.gif\" ".
			"WIDTH=\"10\" BORDER=\"0\">","$PHP_SELF?show=link&mode=edit&link=$id", "");
		$text .= createLink("Delete this link", "<IMG SRC=\"startpage_images/trash.gif\" ".
			"WIDTH=\"10\" BORDER=\"0\">", "$PHP_SELF?show=link&mode=del&link=$id", "");
		$text .= "</TD>";
	} else {
		$text .= "  <TD>&nbsp;</TD>";
	}
	$text .= "</TR>";

	return $text;
}

function print_category($category, $column) {
	global $DBDatabase, $debug, $CombineCategoryChilds, $coltext, $colcount, $show_id;
	$text = "";
	if (!$debug) {
		$text .= printHeader($category["id"],$category["name"]);
	} else {
		$text .= printHeader($category["id"],
			$category["name"]."(".$category["count"].")");
	}
	$resultLinks = Query($DBDatabase,
			"SELECT id, name, link, count FROM links WHERE ".
			"category='".$category["id"]."' AND user='".$show_id."' ORDER BY count DESC, id");
	while ($rowlink = mysqli_fetch_array($resultLinks)) {
		if (!$debug) {
			$text .= printLink($rowlink["id"], $rowlink["link"],
				$rowlink["name"], $category["id"]);
		} else {
			$text .= printLink($rowlink["id"], $rowlink["link"],
					$rowlink["name"]."(".$rowlink["count"].")",
					$category["id"]);
		}
	}
	$coltext[$column] .= $text;
	$colcount[$column] += 1 + mysqli_num_rows($resultLinks);
	mysqli_free_result($resultLinks);

	if ($CombineCategoryChilds) {
		$resultChildCategories = Query($DBDatabase,
			"SELECT id, name, count FROM categories WHERE ".
			"parent='".$category["id"]."' AND user='".$show_id."' ORDER BY count DESC");
		if (mysqli_num_rows($resultChildCategories)) {
			while ($row = mysqli_fetch_array($resultChildCategories)) {
				print_category($row, $column);
			}
			mysqli_free_result($resultChildCategories);
		}
	}
}

$coltext = array();
$colcount = array();

if (!isset($numcol))
	$numcol = $DefaultColumnCount;

for ($i=0; $i < $numcol; $i++) {
	$coltext[$i] = "";
	$colcount[$i] = 0;
}

if ($CombineCategoryChilds) {
	$resultParentCategories = Query($DBDatabase,
		"SELECT id, name, count FROM categories WHERE id != '1' AND ".
		"parent = 1 AND user='".$show_id."' ORDER BY count DESC");
} else {
	$resultParentCategories = Query($DBDatabase,
		"SELECT id, name, count FROM categories WHERE id != '1' ".
		"AND user='".$show_id."' ORDER BY count DESC");
}

if (mysqli_num_rows($resultParentCategories)) {
	while ($row = mysqli_fetch_array($resultParentCategories)) {
		$lowest = $colcount[0];
		$lowest_id = 0;

		for ($i = 1; $i < $numcol; $i++) {
			if ($colcount[$i] < $lowest) {
				$lowest = $colcount[$i];
				$lowest_id = $i;
			}
		}

		print_category($row, $lowest_id);
		$coltext[$lowest_id] .= "<TR><TD COLSPAN=3>&nbsp;</TD></TR>";
		$colcount[$lowest_id] += 1;
	}
	mysqli_free_result($resultParentCategories);
}

if (strlen($HTMLHeader))
	echo "<CENTER><H1>T</H1></CENTER>";
echo "<TABLE BORDER=\"0\" CELLSPACING=\"0\">";
echo "<TR WIDTH=\"100%\">";

foreach ($coltext as $column) {
	echo "<TD WIDTH=\"200\" VALIGN=\"top\">";
	echo "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"0\">";
	echo $column;
	echo "</TABLE></TD>";
	echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
}

echo "</TR>";
echo "</TABLE>";

if ($logged_on) {
	if ($user_id == $show_id) {
		echo createLink("Add a new category", "Add a new category",
			"$PHP_SELF?show=category&mode=add", "");
		echo "<BR>";
	}
	echo createLink("Log off","Log off","$PHP_SELF?action=submitlogoff", "");
	if ($_SESSION['admin'] == 1) {
		echo createLink("User Management", "User Management", "$PHP_SELF?show=users&mode=list", "");
	}
} else
	echo createLink("Log in","Log in","$PHP_SELF?show=login", "");
?>
