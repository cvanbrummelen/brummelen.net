<?php
	include("inc/includes.php");
	setcookie ("brummelen_google", "", time() - 3600);
	$COMMON->redirect("index.php", 0, "");

?>