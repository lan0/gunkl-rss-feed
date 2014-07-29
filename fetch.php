<?php
	include("config.php");

	$link = mysql_connect($db['host'], $db['user'], $db['pw']) OR die("could not connect to database: " . mysql_error());
	
	mysql_select_db($db['database'], $link) OR die("could not select database: " . mysql_error());

	$month = 3; // starts 2000 / 4
	$count = 0;
	
	for($year = 2014; $year <= date("Y"); $year++) { // year 2000 
		
		if($year == date("Y"))
			$to_month = date("n");
		else
			$to_month = 12;
		
		for($month; $month <= $to_month; $month++) {
			
			$content = file_get_contents("http://www.gunkl.at/tips-system/archiv.php?MONTH=$month&YEAR=$year");

			$dom = new DOMDocument();
			$dom->loadHTML($content);

			$items = $dom->getElementsByTagName('p');

			for($i = 1; $i < $items->length; $i++) {
				
				$item = $items->item($i);

				$date = date("Y-m-d", strtotime(substr($item->nodeValue, 0, 10)));

				$text = substr($item->textContent, 11);
			
				$text = utf8_decode($text);
	
				$text = stripslashes($text);
				$text = str_replace("<br>", "\n", $text);
				$text = str_replace("<br />", "\n", $text);

				mysql_query("INSERT INTO tip (datum, text) VALUES ('$date', '" . mysql_escape_string($text) . "')") OR print("could not execute query: " . mysql_error());
				
				$count++;
			}
		
		}
		
		$month = 1;
	}
	
	echo "added $count tips";
	
?>
