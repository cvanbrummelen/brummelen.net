<?php
ob_start();
class Database{
    var $host;
    var $dbname;
    var $username;
    var $password;
    var $link;
    var $querycount;
    var $querytime;
    var $querylog;
    var $insertid;
	var $debug;
	var $rows;
    var $error;
    var $selected;
    var $connected;
    var $highlight_kleuren;
    var $prefix;
    var $tables;
    var $tables_sync;
    function Database($host, $db, $username, $password, $prefix="", $tables=null){
        $this->host = $host;
        $this->dbname = $db;
        $this->username = $username;
        $this->password = $password;
        $this->querycount = 0;
        $this->querylog = array();
        $this->error = "";
        $this->prefix = $prefix;
        $this->tables = $tables;
        //if(!$tables || !$preset){
        //    $this->tables = null;
        //    $this->preset = null;
        //}
        $this->connect();
        $this->highlight_kleuren = array("ff8080", "ffd580", "d5ff80", "80ff80", "80ffd5", "80d4ff", "8080ff", "d580ff", "ff80d4");
    }
    function connect(){
        if(!$this->connected()){
            $this->link = @mysqli_connect($this->host, $this->username, $this->password); // or die ("Could not connect to ".$this->host.", please check your settings<br><br/>"."Error: <font color=#0000FF>".mysql_error()."</font>");
            $this->selected = @mysqli_select_db($this->link, $this->dbname); // or die ("Could not select database ".$this->dbname."<br>");
            $this->tables_sync = true;
            $tables = $this->query("SHOW TABLES",-1);
            if($this->tables && $tables){
                foreach($tables as $k => $table){
                    $tables_db[] = $table[0];
                }
                foreach($this->tables as $k => $table){
                    if(!in_array($this->prefix.$table, $tables_db)){
                        $this->tables_sync = false;    
                    }
                }
            }
        }
    }
    function connected(){
        if(!$this->link || !$this->selected) return false;
        return true;
    }
    function tables_synced(){
        return $this->tables_sync;
    }
    function close(){
        @mysqli_close($this->link) or die ("Could not disconnect from ".$this->host."<br>");
    }
    function query($query, $display=false){
        global $SETTINGS;
        global $COMMON;

        if($this->prefix){
            $query = $query.";";
            foreach($this->tables as $k => $table){    
                $query = str_replace(" ".$table." ", " ".$this->prefix.$table." ", $query);
                $query = str_replace(" ".$table.".", " ".$this->prefix.$table.".", $query);
                $query = str_replace(" ".$table.",", " ".$this->prefix.$table.",", $query);
                $query = str_replace(" ".$table.";", " ".$this->prefix.$table.";", $query);
            }
        }
        if($this->connected())
        {
        	$SETTINGS["debug"] = null;
			$rows = null;
            $startTime = microtime();
            if($SETTINGS["debug"] && $SETTINGS){
                $result = @mysqli_query($this->link, $query) or die ("Your query \"<font color=#FF0000>".$query."</font>\" is not valid.<br><br/>"."Error: <font color=#0000FF>".mysqli_error()."</font>");
            } else {
                $result = @mysqli_query($this->link, $query);
				if(!$result && $COMMON) $COMMON->log("Your query \"<font color=#FF0000>".$query."</font>\" is not valid.<br><br/>"."Error: <font color=#0000FF>".$this->link->error."</font>");
            }
            $this->insertid = mysqli_insert_id($this->link);
            $stopTime = microtime();
            $this->recordtime($stopTime - $startTime);
            while ($row = @mysqli_fetch_array($result)) {
                $rows[] = $row;
            }
            $this->querycount++;
            $this->querylog($query, $stopTime-$startTime, $rows, $display);
            return $rows;
        } else {
            return false;
        }
    }
    
    function insertQuery($query, $display=false){
    	global $SETTINGS;
    	global $COMMON;
    	$return = false;
    	if($this->prefix){
    		$query = $query.";";
    		foreach($this->tables as $k => $table){
    			$query = str_replace(" ".$table." ", " ".$this->prefix.$table." ", $query);
    			$query = str_replace(" ".$table.".", " ".$this->prefix.$table.".", $query);
    			$query = str_replace(" ".$table.",", " ".$this->prefix.$table.",", $query);
    			$query = str_replace(" ".$table.";", " ".$this->prefix.$table.";", $query);
    		}
    	}
    	if($this->connected())
    	{
    		$SETTINGS["debug"] = null;
    		$rows = null;
    		$startTime = microtime();

    		$result = @mysqli_query($this->link, $query);
    		if(!$result && $COMMON) 
    		{
    			$COMMON->log("Your query \"<font color=#FF0000>".$query."</font>\" is not valid.<br><br/>"."Error: <font color=#0000FF>".$this->link->error."</font>");
    			$return = false;
    		}
    		else
    		{
    			$return = true;
    		}
    		
    		$this->insertid = mysqli_insert_id($this->link);
    		$stopTime = microtime();
    		$this->recordtime($stopTime - $startTime);
    		$this->querycount++;
    		$this->querylog($query, $stopTime-$startTime, $rows, $display);
    		return $return;
    	} else {
    		$return = false;
    	}
    	return $return;
    }
    function insertid(){
        return $this->insertid;
    }
    function querycount(){
        $were = "was";
        $s = "y";
        if($this->querycount > 1){
            $s = "ies";
            $were = "were";
        }
        if($this->querycount == 0){
            return "No queries were executed.";
        }
        return $this->querycount." quer$s $were executed in ".$this->querytime()." seconds";    
    }
    function recordtime($time){
        $this->querytime += $time;
    }
    function querytime(){
        return round($this->querytime,4);
    }
    function querylog($query=false, $time=false, $result=false, $display=false){
        $table = "";
        if($query && $time){
            $this->querylog[] = array($query, $time, $result, $display);            
        } else if($this->connected()) {
            $styletd = "style='border-top: 1px solid #eee; border-right: 1px solid #eee; padding: 2px;'";
            $styleth = "style='border-bottom: 1px solid #000; background: #000; color: #fff;'";
            $caption = "<strong>".$this->querycount()."</strong> for <strong>$this->username@$this->host</strong> on <strong>$this->dbname</strong>";
            $table .= "<br/><br/><table class='overzicht'> <caption>".$caption."</caption>";
            if($this->querycount){
                $table .= "<tr class='overzichthover'><th $styleth>#</th><th $styleth>Query</th><th $styleth>Time</th><th $styleth>Result</th></tr>";
                $counter=0;
                foreach($this->querylog as $query){
                    if($query[3] != -1){
                        $counter++;
                        $s = "";
                        if($query[3]) $s = " style='background-color: #".$this->highlight_kleuren[($query[3]-1)]."'";
                        $table .="<tr class='overzichthover'>";
                        $table .="<td $styletd>".$counter."</td>";
                        $table .="<td $styletd>".$query[0]."</td>";
                        $table .="<td $styletd>".$query[1]."</td>";
                        
                        $c=0;
                        $table .= "<td $styletd>";
                        $columns = array();
                        if($query[2]){
                            $table .= "<table class='overzicht'>";
                            foreach($query[2] as $result){
                                if($c++ == 0){
                                    $table .= "<tr class='overzichthover'>";
                                    $table .="<th $styleth>#</th>";
                                    foreach($result as $column => $value){
                                        if(is_string($column)){
                                            $columns[] = $column;
                                            $table .="<th $styleth>$column</th>";
                                        }
                                    }    
                                    $table .=  "</tr>";
                                }
                                $table .= "<tr class='overzichthover'>";
                                $table .= "<td $styletd>$c</td>";
                                foreach($columns as $column){
                                    $table .="<td $styletd>$result[$column]</td>";
                                }
                                $table .= "</tr>";
                            }
                            $table .= "</table>";
                        } else {
                            $table .= "No result";
                        }
                        $table .= "</td>";                
                        $table .="</tr>";
                        
                    }
                }
            }
            $table .= "</table>";
            return $table;
        }
    }
	
	function lastInsertId()
	{
		return mysqli_insert_id($this->link);
	}
	
	function injection($value){
	    if( get_magic_quotes_gpc() ){
	          $value = stripslashes( $value );
	   	}
	    if( function_exists( "mysql_real_escape_string" ) ){
	          $value = mysqli_real_escape_string($this->link, $value );
	    }
    	else{
          $value = addslashes( $value );
    	}
		return $value;
	}
}
?> 
