<?php
class common {
    // een functie om te redirecten naar een gegeven pagina, na een bepaalde tijd
    var $pageinfo;


	// Redirect functie
    function redirect($page, $timeout=0, $message='U word nu doorverwezen'){
        global $OPTIONS;
        $time = ($timeout*1000);
        if($page[0] == "/") $page = $OPTIONS->options("database_host").substr($page, 1);
        echo "<script language=\"JavaScript\" type=\"text/javascript\">setTimeout(\"document.location = \'$page\'\", $time);</script>";
        echo "<p>$message</p>";
        exit;
    }
    
    // WYSIWYG Editor voor forms
    function FCKeditor($name, $value=''){
            $oFCKeditor = new FCKeditor($name);
            $oFCKeditor->Config['EnterMode'] = 'br';
            $oFCKeditor->BasePath = 'inc/fckeditor/';
            $oFCKeditor->Value = $value;
            $oFCKeditor->Width = '100%' ;
            $oFCKeditor->Height = '400' ;
            //\$oFCKeditor->ToolbarSet = 'MyToolbar' ;
            $oFCKeditor->ToolbarSet = 'Default' ;
            $oFCKeditor->Create() ;
        }
    
    // Nieuwe WYSIWYG Editor
    
    /*function FCKeditor($name, $value='')
    {   
		$CKEditor = new CKEditor();
		$CKEditor->returnOutput = true;
		$CKEditor->basePath = 'inc/ckeditor';
		$CKEditor->config['width'] = 800;
		$CKEditor->textareaAttributes = array("cols" => 80, "rows" => 10);
		$initialValue = $value;		
		$code = $CKEditor->editor($name, $initialValue);
		
		echo $code;  
    }*/

    // converteerd ON en OFF naar 1 of 0
    function getBoolean($str){
        if($str=="on"){
            return "1";
        }else{
            return "0";
        }
    }
    
    function validEmail($email){
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
            return true;
        }
        else{
            return false;
        }
    }
    
    function datetime(){
		$mysqldate = date( 'Y-m-d H:i:s');
		return $mysqldate;
    }
    
    function log($action, $description=''){
    	global $DATABASE;
    	global $OPTIONS;
    	global $LANGUAGE;
    	$date = date("d-m-y - H:i:s", time());
    	/*if(!$_SESSION[$OPTIONS->options("applicatienaam")])
    	{ 
    		$user = $LANGUAGE[onbekend]; 
    	} 
    	else 
    	{ 
    		$user = ucfirst($_SESSION[$OPTIONS->options("applicatienaam")]); 
    	}*/
    	//$log_insert = $DATABASE->query("INSERT INTO log (action, description, user, date) VALUES ('".$DATABASE->injection($action)."', '".$DATABASE->injection($description)."', '".$DATABASE->injection($user)."', '".$DATABASE->injection($date)."')");
    }
    
    function logView($s, $n, $search)
    {
    	global $DATABASE;
    	global $OPTIONS;
    	
    	echo "<h1><img src='inc/images/icons/log.png' border='0'>  Log</h1>";
    	
    	if(empty($search))
    	{	
    		$log = $DATABASE->query("SELECT * FROM log ORDER BY id DESC limit $s, $n ");
    	}
    	else
    	{
    		$log = $DATABASE->query("SELECT * FROM log WHERE (description) LIKE '%$search%' OR (user) LIKE '%$search%' OR (action) LIKE '%$search%' ORDER BY id DESC limit $s, $n ");
    		$count = $DATABASE->query("SELECT count(*) as Counter FROM log WHERE (description) LIKE '%$search%' OR (user) LIKE '%$search%' OR (action) LIKE '%$search%' ORDER BY id DESC limit $s, $n ");
    		$count = $count[0];
    	}
    	if($log)
    	{
    		echo "<center>";
    		
    		?>
    		<form action="admin.php?pagina=log&logView=true&start=0&view=<?php echo $n; ?>" method="post">
    		<input type="text" name="search" value=""/>
    		<input type="submit" value="Zoeken">
    		</form><br>
    		<?php
    		echo "<center><a href='admin.php?pagina=log&logView=true&start=0&view=10&search=$search'>10</a> -  <a href='admin.php?pagina=log&logView=true&start=0&view=20&search=$search'>20</a> -  <a href='admin.php?pagina=log&logView=true&start=0&view=30&search=$search'>30</a> -  <a href='admin.php?pagina=log&logView=true&start=0&view=40&search=$search'>40</a> -  <a href='admin.php?pagina=log&logView=true&start=0&view=50&search=$search'>50</a><br></center>"; 
    		if(!$s == 0)
	    	{
	    		$new = $s-$n;
	    		echo "<a href='admin.php?pagina=log&logView=true&start=$new&view=$n&search=$search'><img src='inc/images/icons/back.png'></a>";
	    	}
	    		$new2 = $s+$n;
	    		$log2 = $DATABASE->query("SELECT * FROM log ORDER BY id DESC limit $new2, $n ");
	    		if($log2)
	    		{
	    			if($count)
	    			{
	    				echo "<br>$LANGUAGE[zoekresultaten]: <b>". $search."</b><br><br>";
	    				if($count['Counter'] > $n)
	    				{
	    					echo "<a href='admin.php?pagina=log&logView=true&start=$new2&view=$n&search=$search'><img src='inc/images/icons/next.png'></a>";
	    				}
	    			}
	    			else 
		    		{
		    			echo "<a href='admin.php?pagina=log&logView=true&start=$new2&view=$n&search=$search'><img src='inc/images/icons/next.png'></a>";
		    		}
	    		}
	    	echo "</center><center>";
    		echo "<br><br>";
    		echo "<table class='overzichtadmin'><th>ID</th><th>Action</th><th>Description</th><th>User</th><th>Date</th>";
	    	foreach($log as $logs)
	    	{
	    		echo "<tr class='overzichthover'><td>$logs[id]</td><td>$logs[action]</td><td>$logs[description]</td><td>$logs[user]</td><td>$logs[date]</td></tr>";	
	    	}
	    	echo "</table>";
	    	echo "<br>";
	    	if(!$s == 0)
	    	{
	    		$new = $s-$n;
	    		echo "<a href='admin.php?pagina=log&logView=true&start=$new&view=$n'><img src='inc/images/icons/back.png'></a>     ";
	    	}
	    		$new2 = $s+$n;
	    		$log2 = $DATABASE->query("SELECT * FROM log ORDER BY id DESC limit $new2, $n ");
	    		if($log2)
	    		{
	    			if($count)
	    			{
	    				if($count['Counter'] > $n)
	    				{
	    					echo "<a href='admin.php?pagina=log&logView=true&start=$new2&view=$n&search=$search'><img src='inc/images/icons/next.png'></a>";
	    				}
	    			}
	    			else 
		    		{
		    			echo "<a href='admin.php?pagina=log&logView=true&start=$new2&view=$n&search=$search'><img src='inc/images/icons/next.png'></a>";
		    		}
	    		}	
	    	echo "</center>";	
    	}
    }
    
    function loggedIn(){
    	global $OPTIONS;
    	global $LANGUAGE;
    	if ($_SESSION[$OPTIONS->options("applicatienaam")]){
    		return ucfirst($_SESSION[$OPTIONS->options("applicatienaam")]);
    	}
    	else
    	{
    		return $LANGUAGE['niet_ingelogd'];
    	}
    }
    
    function worldStats()
    {
    
    ?>
    	<div id="widgetIframe"><iframe width="49%" height="600" src="http://brumpc.nl/inc/modules/piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=VisitsSummary&actionToWidgetize=index&idSite=1&period=day&date=2010-08-21&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
		<iframe width="49%" height="600" src="http://brumpc.nl/inc/modules/piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=UserCountryMap&actionToWidgetize=worldMap&idSite=1&period=day&date=2010-08-21&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>
    <?php
    
    }

    function contactForm($status='')
    {
      global $SETTINGS;
      if($status == 'send')
      {
			$recipient=$SETTINGS['email']; //hier je emailadres
			$subject=$SETTINGS['onderwerp']; //hier vul je een subjectnaam in zoiets als 'Contact' of 'Info +sitenaam+'
			$from =$_POST['email'];
			$email = $_POST['email'];
			$name = $_POST['contactpersoon'];

			$content=	
			"<html><body bgcolor='#fff'>
			<table><tr><td><p><h2>Contact Formulier<h2></p></td></tr>
			<tr><td><br><b>Bedrijfsnaam: </b>".$_POST['bedrijfsnaam']. " 
			<br><b>Contactpersoon: </b>".$_POST['gender']." ".$_POST['contactpersoon']. " 
			<br><b>Adres: </b>".$_POST['adres']. " 
			<br><b>Postcode: </b>".$_POST['postcode']." 
			<br><b>Plaats: </b>".$_POST['plaats']."
			<br><b>Telefoon nr: </b>".$_POST['telefoon']."
			<br><b>E-mail: </b>".$_POST['email']."
			<br><b>Opmerking: </b>".$_POST['bericht']."
			</td></tr></table></body></html>";
			
			if($_POST['email'] == "" || $_POST['telefoon'] == ""  || $_POST['contactpersoon'] == "" )
			{
				$this->redirect("index.php?pagina=contact", 3,"U heeft niet alle verplichte velden ingevuld.");
			}
			else
			{
				mail($recipient, $subject, $content, "From: ".$name." <".$email.">\n"."MIME-Version: 1.0\n" ."Content-type: text/html; charset=iso-8859-1"); 
			}
				echo "Uw mail is met succes verstuurd!"; 
      }
      else
      {
      	?>
			<table>
			<td class='contactform'>
			<form action="index.php?pagina=contact&status=send" method='post'>
			<tr><td><label>Bedrijfsnaam:</label></td><td> <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" value=""/></td></tr>
			<tr><td><label>* Contactpersoon:</label></td><td> <input type="text" id="contactpersoon" name="contactpersoon" value=""/></td></tr>
			<tr><td><input type="radio" value="Dhr." name="gender" class='radio'> Dhr.  </td><td><input type="radio" value="Mevr" name="gender" class='radio'> Mevr</td></tr>
			<tr><td><label>Adres:</label> </td><td><input type="text" id="adres" name="adres" value=""/></td></tr>
			<tr><td><label>Postcode:</label> </td><td><input type="text" id="postcode" name="postcode" value=""/></td></tr>
			<tr><td><label>Plaats:</label></td><td><input type="text" id="plaats" name="plaats" value=""/></td></tr>
			<tr><td><label>* Telefoon nr:</label></td><td><input type="text" id="telefoon" name="telefoon" value=""/></td></tr>
			<tr><td><label>* E-mail:</label></td><td><input type="text" id="email" name="email" value=""/></td></tr>
			<tr><td><label>Opmerking:</label></td><td>
			<textarea name="bericht" cols="25" rows="5" id="bericht"> 
			</textarea> </td></tr>
			<tr><td colspan='2'>* Verplicht</td></tr>
			<tr><td><input type="submit" name="Submit" value="Verzenden"></td><td> 
			<input type="reset" name="Submit2" value="Wissen"> </td></tr>
			 
			</form>
			</td></tr>
			</table>
      	
      	<?php
      }
    }


}
?> 