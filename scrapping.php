<?php

require 'simple_html_dom.php';
libxml_use_internal_errors(true);
set_time_limit(500); // 

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
$domain = "https://www.domaza.bg";
$maxPages = 1;
for ($i = 1; $i <= $maxPages; $i++) {
	curl_setopt($curl, CURLOPT_URL, "https://www.domaza.bg/%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8/_page/$i");
	$html = curl_exec($curl);
	$urls = getURL($html,$domain);
	foreach ($urls as $url){
		$info =  getInfo($url);
		$convert = convertToCSV($array);
	}

}

function getURL($html,$domain){
	$doc = new DOMDocument();
	$doc->loadHTML($html);
	$xpath = new DOMXpath($doc);
	$result = [];
	if(!empty($doc)){
		foreach ($xpath->query('//div[contains(@class, "col-xs-12 agency_name")]') as $element) {
			$links = $element->getElementsByTagName('a');
			foreach ($links as $link) {
				$result[] = [
					'link' => $domain.$link->getAttribute('href'),
					'title' => $link->getAttribute('title')
				];
			}
		}
		return $result;
	}
}

function getINFO($doc,$url){
	$new_html = file_get_html($url['link']);
	$doc->loadHTML($new_html);
	$xpath = new DOMXpath($doc);
	$array = [];
	foreach($xpath->query('//div[contains(@class, "col-xs-9 agency-offices-info")]') as $element){
		$content = $element->getElementsByTagName('li');
		foreach ($content as $contents) {
			$string = $contents->C14N();
			$array[] = strip_tags($string);
		}
	}
	return $array;
}

function convertToCSV($array){
	$f = fopen('test.csv', 'a');

	fputcsv($f, $array);
	fclose($f);	
}

?>