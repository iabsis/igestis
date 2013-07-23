<?php
/*
// If a little malicious guy attempt to launche this file directly, application stop with the message below ...
if(!defined("INDEX_LAUNCHED")&& !defined("GENERAL_INDEX_REQUEST_LAUNCHED")) die("Hacking attempt");
define(IABSIS_NEWS_URL, "http://iabsis.com/index.php?mact=CGFeedMaker,cntnt01,default,0&cntnt01feed=News2&cntnt01showtemplate=false&cntnt01returnid=15");


// Ajout d'une occurrence de l'applet dans le getionnaire d'applet
$applet_id = $this->add_applet("IABSIS_NEWS", "Quoi de neuf chez iabsis ?");


// Création du contenu de l'applet
$data = "";
$file = $application->get_html_content("plugins/iabsis_news.htm");
$data = $file;


// Ajout du contenu de l'applet
$this->set_applet_data($applet_id, $data);


// Fonction utilisables pour cet applet

function get_iabsis_news(&$applet_collection, &$application) {	
	if(mktime((int)date("H"), (int)date("i"), (int)date("s"), (int)date("m"), (int)date("d"), (int)date("Y")) - $applet_collection->get_option_value("IABSIS_NEWS", "LAST_RSS_UPDATE") > 600) {
		// Si on a pas updaté les flux depuis plus de 10 min, alors on les réimporte	
		
		$month_list = array(
						"Jan" => 1,
						"Feb" => 2,
						"Mar" => 3,
						"Apr" => 4,
						"May" => 5,
						"Jun" => 6,
						"Jul" => 7,
						"Aug" => 8,
						"Sep" => 9,
						"Oct" => 10,
						"Nov" => 11,
						"Dec" => 12,
						);
						
		// Création de l'objet de parsing XML
		$dom = new DomDocument();
		$dom->load(IABSIS_NEWS_URL);
		
		// Suppression de toutes les news
		$sql = "TRUNCATE APPLET_IABSIS_NEWS_LIST";
		mysql_query($sql) or die(mysql_error() . $sql);
		
		$item_list = $dom->getElementsByTagName('item');
		
		foreach($item_list as $item) {
			$title = $item->getElementsByTagName('title')->item(0)->nodeValue;
			$link  = $item->getElementsByTagName('link')->item(0)->nodeValue;
			$description  = $item->getElementsByTagName('description')->item(0)->nodeValue;
			$date  = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;
			
			preg_match("/([A-Z]{3}), ([0-9]{1,2}) ([A-Z]{3}) ([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2}) \+([0-9]{4})/i", $date, $fields);
			$date = date("Y-m-d H:i:s", mktime((int)$fields[5], (int)$fields[6], (int)$fields[7], $month_list[$fields[3]], (int)$fields[2], (int)$fields[4]));

			
			// Création des news
			$sql = "INSERT INTO APPLET_IABSIS_NEWS_LIST (title, link, description, date) VALUES (
					'" . mysql_real_escape_string($title) . "',
					'" . mysql_real_escape_string($link) . "',
					'" . mysql_real_escape_string($description) . "',
					'" . $date . "')";
			mysql_query($sql) or die(mysql_error() . $sql);
		}
		
		$req = mysql_query($sql) or die(mysql_error() . $sql);
		
		$time = time();
		$applet_collection->set_option("IABSIS_NEWS", "LAST_RSS_UPDATE", $time);
	}
	
	$sql = "SELECT title, DATE(date) AS date, description FROM APPLET_IABSIS_NEWS_LIST ORDER BY date DESC LIMIT 10";
	$req = mysql_query($sql) or die(mysql_error() . $sql);
	$cpt = 0;
	while($data = mysql_fetch_array($req)) {		
		$application->add_block("IABSIS_NEWS", array(
			"CLASS" => ($cpt++%2) ? "ligne1" : "ligne2", 
			"title" => $data['title'], 
			"description" => $data['description'],
			"id" => $cpt,
			"short_description" => cut_sentence($data['description'], 40),
			"date" => convert_date_en_to_fr($data['date'])));
	}
	
	$application->add_var("IABSIS_RSS_LINK", IABSIS_NEWS_URL);

}
*/