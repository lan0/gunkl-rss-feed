<?php
	include("config.php");

	error_reporting('E_NONE');

	echo "[" . date("Y-m-d H:i:s") . "] ";
	
	$link = mysql_connect($db['host'], $db['user'], $db['pw']) OR die("could not connect to database: " . mysql_error());
	
	mysql_select_db($db['database'], $link) OR die("could not select database: " . mysql_error());
	
	$content = file_get_contents("http://www.gunkl.at/");

	$dom = new DOMDocument();
	$dom->loadHTML($content);

	$items = $dom->getElementsByTagName("font");

	$count = 0;
	
	for($i = 1; $i < $items->length; $i++) {
		
		$item = $items->item($i);

		$attributes = $item->attributes;
		
		$candidate = FALSE;
		
		for($k = 0; $k < $attributes->length; $k++) {
			if($item->attributes->item($k)->name == "face")
				$candidate = TRUE;
			if($candidate && $item->attributes->item($k)->name == "size") {
				$date = date("Y-m-d");
		
				$text = trim($item->childNodes->item(4)->textContent);

				$text = utf8_decode($text);
				
				$text = stripslashes($text);
				$text = str_replace("<br>", "\n", $text);
				$text = str_replace("<br />", "\n", $text);

				if(mysql_num_rows(mysql_query("SELECT datum FROM tip WHERE datum = '$date' AND text = '" . mysql_escape_string($text) . "'")) == 0) {
					mysql_query("INSERT INTO tip (datum, text) VALUES ('$date', '" . mysql_escape_string($text) . "')") OR die("could not execute query: " . mysql_error());
					$count++;
				}
				
				break;
			}
				
		}
		
	}
	
	echo "added $count tips\n";
	
?>
