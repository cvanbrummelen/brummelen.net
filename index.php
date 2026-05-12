<?php

require("db.php");
require("helpers.php");
include("inc/includes.php");

Connect();

$DefaultUser = get_db_var($DBDatabase, "default_user");
$AdminUser = get_db_var($DBDatabase, "admin_user");

$mes_correct = 0;
$admin = 0;
$active_user = 0;
$debug = 0;
$user = "";

session_start();

$action = get_input_var("action");

if (strlen($action)) {
	if ($action == "submitlogin") {
		if (isset($_SESSION['user_id'])) {
			$_SESSION = array();
			session_destroy();
		}

		if (strlen($_POST["c_username"]) &&
			strlen($_POST["c_password"])) {
			
			$result = Query($DBDatabase, "SELECT id, password FROM users WHERE ".
					"username='".get_query_post_var("c_username")."'");
			if ($row = mysqli_fetch_array($result)) {
				$c_id = $row["id"];
				$c_password = $row["password"];
				mysqli_free_result($result);

				if ($c_password == md5($_POST["c_password"])) {
					$_SESSION['user_id'] = $c_id;
					$_SESSION['admin'] = 0;
					$_SESSION['show_id'] = $c_id;
					
					if ($_POST["c_username"] == $AdminUser)
						$_SESSION['admin'] = 1;
				}
			}
		}
		if (!isset($_SESSION['user_id'])) {
			echo "Failed to log in.<br/>\n";
		}
	}
	if ($action == "submitlogoff") {
		$_SESSION = array();
		session_destroy();
	}
}

// Guarantee that in a new session, the default account is viewed if nothing is selected.
//
if (!isset($_SESSION['show_id']))
	$user = $DefaultUser;

if (isset($_SESSION['user_id'])) {
	$admin = $_SESSION['admin'];
	$user_id = $_SESSION['user_id'];
	$logged_on = true;
} else {
	$logged_on = false;
	$admin = 0;
	$user_id = -1;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
echo "<head>\n";
echo "<meta name=\"description\" content=\"$HTMLDescription\">\n";
echo "<meta name=\"keywords\" content=\"$HTMLKeywords\">\n";
echo "<title>$HTMLTitle</title>\n";
echo "<link rel=\"stylesheet\" href=\"https://www.brummelen.net/$HTMLStyleSheet\" type=\"text/css\">\n";
echo "</head>\n";
echo "<body>";

if($_GET['p'] == "startpagina")
{
	$startpagina = "id='current'";
}
elseif($_GET['p'] == "dvd")
{
	$dvd = "id='current'"; 
}
else
{
	$startpagina = "id='current' ";
}
echo $_GET['p'];
$googleUser = $DATABASE->query("SELECT * FROM google_users WHERE secure = '".$_COOKIE['brummelen_google']."'");
?>
<div class='container'>
<div style='display: none;' class='top_bar'>
	<div class='top_bar_links'>
	<div class="buttons" <?php echo $startpagina; ?> OnClick="javascript: window.location = '/?p=startpagina';">Startpagina</div>
	<?php if($googleUser)
		{
		?>
			<div class="buttons" <?php echo $dvd; ?> OnClick="javascript: window.location = '/?p=dvd';">DVD</div>
		<?php
		}
		?>
	</ul>
	</div>
	<div class='login'>
		<?php
		
		
		if($googleUser)
			{
			$googleUser = $googleUser[0];
			echo "Welkom ".ucfirst($googleUser['username']). "<a href='logout.php'><img src='settings.png' border='0'></a>";	
			}	
		else
			{
				?>
				<a class="rpxnow" onclick="return false;"href="https://brumpc.rpxnow.com/openid/v2/signin?token_url=http%3A%2F%2Fwww.brummelen.net%2Flogin.php"><img src='settings.png' border='0'></a>
				<?php
			}
		?>

		</div>
</div>
<div class='top_bar_bottom'>
<center>
<form action="https://www.google.com/cse" id="cse-search-box" target="_blank">
	<div>
    <input height='20' type="hidden" name="cx" value="partner-pub-9435340088862159:z2ups3kf0ui" />
    <input height='20' type="hidden" name="ie" value="ISO-8859-1" />
    <input height='20' type="text" name="q" size="60"/>
    <input height='20' type="submit" name="sa" value="Zoeken" />
  </div>
</form></center>
<script type="text/javascript" src="https://www.google.com/cse/brand?form=cse-search-box&amp;lang=nl"></script> 
</div>
<div class='content'>
	<center>
<?php
if(!($_GET['p']))
{
	include('startpage.php');
}
if($_GET['p'] == "startpagina")
{
	include('startpage.php');
}
if($googleUser)
	{
		if($_GET['p'] == "dvd")
		{
			include('dvd.php');
		}
	}


?>
<br><br></br></br>
<script type="text/javascript"><!--
google_ad_client = "pub-9435340088862159";
/* 468x60, gemaakt 9-11-10 */
google_ad_slot = "6263039954";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</center>
</div></div>
<script type="text/javascript">
  var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");
  document.write(unescape("%3Cscript src='" + rpxJsHost +
"rpxnow.com/js/lib/rpx.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
  RPXNOW.overlay = true;
  RPXNOW.language_preference = 'en';
</script>
</body>
</html>
