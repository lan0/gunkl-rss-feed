<?php
  include ("config.php");

  // This is a minimum example of using the class
  include("FeedWriter.php");
  
  $link = mysql_connect($db['host'], $db['user'], $db['pw']) OR die("could not connect to database: " . mysql_error());
	
  mysql_select_db($db['database'], $link) OR die("could not select database: " . mysql_error());
  
  //Creating an instance of FeedWriter class. 
  $TestFeed = new FeedWriter(RSS2);
  
  //Setting the channel elements
  //Use wrapper functions for common channel elements
  $TestFeed->setTitle('Gunkls Tip des Tages');
  $TestFeed->setLink('http://gunkl.ich-checks.net/feed.php');
  $TestFeed->setDescription('Gunkls Tip des Tages');
  
  //Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
  $TestFeed->setImage('Gunkls Tip des Tages','http://gunkl.ich-checks.net/feed.php','http://gunkl.ich-checks.net/Gunkellogo_white.gif');
 
	mysql_query("SET NAMES utf8");
 
	//Retriving informations from database addin feeds
	$result = mysql_query("SELECT datum, text FROM tip ORDER BY datum DESC LIMIT 50") OR die("could not execute query: " . mysql_error());

	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();
		
		//Add elements to the feed item    
		$newItem->setTitle($row['datum']);
		$newItem->setLink("");
		$newItem->setDate($row['datum']);
		$newItem->setDescription($row['text']);
		
		//Now add the feed item
		$TestFeed->addItem($newItem);
	}
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->genarateFeed();
  
?>
