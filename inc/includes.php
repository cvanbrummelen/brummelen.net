<?php
    session_start();

	include("inc/settings.php");
    //Include classes
    include("inc/classes/database.php");
    include("inc/classes/common.php");

    $DATABASE = new Database($SETTINGS[database_host], $SETTINGS[database_schema],  $SETTINGS[database_gebruiker], $SETTINGS[database_wachtwoord], $SETTINGS[database_prefix], $TABLES);
	$COMMON = new common();
    

?> 