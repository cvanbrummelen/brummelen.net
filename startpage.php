<?php
echo "\n";

if (strlen($action) != 0 && $logged_on) {
	if ($action == "submitaddcategory") {
		$c_name = get_query_post_var("c_name");
		$c_parent = get_query_post_var("c_parent");

		if (!strlen($c_name) || !strlen($c_parent)) {
			echo "Failure: All values should be entered.\n";
		} else {
			$result = Query($DBDatabase,
				"INSERT INTO categories (name, parent, user) ".
				"VALUES ('$c_name', '$c_parent', '$user_id')");

			if ($result) {
				if ($mes_correct)
					echo "Category is added.\n";
			} else {
				echo "Failure: addcategory.\n";
			}
		}
	}

	if ($action == "submiteditcategory") {
		$c_id = get_query_post_var("c_id");
		$c_name = get_query_post_var("c_name");
		$c_parent = get_query_post_var("c_parent");

		if (!strlen($c_id) || !strlen($c_name) ||
			!strlen($c_parent)) {
			echo "Failure: All values should be entered.\n";
		} else if ($c_id == $c_parent) {
			echo "Failure: Cannot become own parent.\n";
		} else {
			$result = Query($DBDatabase,
				"UPDATE categories SET name='$c_name',".
				"parent='$c_parent' WHERE id='$c_id' AND user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Category is edited.\n";
			} else {
				echo "Failure: editcategory.\n";
			}
		}
	}

	if ($action == "submitdelcategory") {
		$c_id = get_query_post_var("c_id");
		$c_parent = get_query_post_var("c_parent");

		if (!strlen($c_id) || !strlen($c_parent)) {
			echo "Failure: Not all values are given.\n";
		} else {
			$result = Query($DBDatabase,
				"DELETE FROM links WHERE category='$c_id' AND user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Links in category are deleted.\n";
			} else {
				echo "Failure: delcategory.\n";
			}

			$result = Query($DBDatabase,
				"DELETE FROM categories WHERE id='$c_id' AND user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Category is deleted.\n";
			} else {
				echo "Failure: delcategory.\n";
			}

			$result = Query($DBDatabase,
				"UPDATE categories SET parent='$c_parent' ".
				"WHERE parent='$c_id' AND user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Child categories are updated.\n";
			} else {
				echo "Failure: delcategory.\n";
			}
		}
	}

	if ($action == "submitaddlink") {
		$c_name = get_query_post_var("c_name");
		$c_link = get_query_post_var("c_link");
		$c_category = get_query_post_var("c_category");

		if (!strlen($c_name) || !strlen($c_link) ||
			!strlen($c_category)) {
			echo "Failure: All values should be entered.\n";
		} else {
			$result = Query($DBDatabase,
				"INSERT INTO links (name, link, category, user) ".
				"VALUES ('$c_name', '$c_link', '$c_category', '$user_id')");

			if ($result) {
				if ($mes_correct)
					echo "Link is added.\n";
			} else {
				echo "Failure: addlink.\n";
			}
		}
	}

	if ($action == "submiteditlink") {
		$c_id = get_query_post_var("c_id");
		$c_name = get_query_post_var("c_name");
		$c_link = get_query_post_var("c_link");
		$c_category = get_query_post_var("c_category");

		if (!strlen($c_id) || !strlen($c_name) || !strlen($c_link) ||
				!strlen($c_category)) {
			echo "Failure: All values should be entered.\n";
		} else {
			$result = Query($DBDatabase,
				"UPDATE links SET name='$c_name', ".
				"link='$c_link', category='$c_category' ".
				"WHERE id='$c_id' and user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Link is edited.\n";
			} else {
				echo "Failure: editlink.\n";
			}
		}
	}

	if ($action == "submitdellink") {
		$c_id = get_query_post_var("c_id");

		if (!strlen($c_id)) {
			echo "Failure: ID not given.\n";
		} else {
			$result = Query($DBDatabase,
				"DELETE FROM links WHERE id='$c_id' and user='$user_id'");

			if ($result) {
				if ($mes_correct)
					echo "Link is deleted.\n";
			} else {
				echo "Failure: dellink.\n";
			}
		}
	}

	if ($action == "submitadduser") {
		$c_username = get_query_post_var("c_username");
		$c_password1 = get_query_post_var("c_password1");
		$c_password2 = get_query_post_var("c_password2");

		if (!strlen($c_username) || !strlen($c_password1) ||
			!strlen($c_password2)) {
			echo "Failure: All values should be entered.\n";
		} else if ($c_password1 != $c_password2) {
			echo "Failure: Passwords are not equal.\n";
		} else {
			$result = Query($DBDatabase,
				"INSERT INTO users (username, password) ".
				"VALUES ('$c_username', MD5('$c_password1'))");

			if ($result) {
				if ($mes_correct)
					echo "User is added.\n";
			} else {
				echo "Failure: adduser.\n";
			}
		}
	}

	if ($action == "submitdeluser") {
		$c_id = get_query_post_var("c_id");

		if (!strlen($c_id)) {
			echo "Failure: Not all values are given.\n";
		} else {
			$result = Query($DBDatabase,
				"DELETE FROM links WHERE user='$c_id'");

			if ($result) {
				if ($mes_correct)
					echo "Links for user are deleted.\n";
			} else {
				echo "Failure: deluser.\n";
			}

			$result = Query($DBDatabase,
				"DELETE FROM categories WHERE user='$c_id'");

			if ($result) {
				if ($mes_correct)
					echo "Categories for user are deleted.\n";
			} else {
				echo "Failure: deluser.\n";
			}

			$result = Query($DBDatabase,
				"DELETE FROM users WHERE id='$c_id'");

			if ($result) {
				if ($mes_correct)
					echo "User is deleted.\n";
			} else {
				echo "Failure: deluser.\n";
			}
		}
	}

}

$show = get_input_var("show", "main");
$user = get_input_var("user", $user);

if (strlen($user)) {
	$result = Query($DBDatabase, "SELECT id FROM users WHERE ".
			"username='$user'");
	if (mysqli_num_rows($result) == 1) {
		if ($row = mysqli_fetch_array($result))
			$_SESSION["show_id"] = $row["id"];
	} else
		$_SESSION["show_id"] = -1;
	mysqli_free_result($result);
} 

$show_id = $_SESSION["show_id"];

if ($show == "login" || ($logged_on && $show_id == $user_id))
	include ("startpage-".$show.".php");
else
	include ("startpage-main.php");
  
?>