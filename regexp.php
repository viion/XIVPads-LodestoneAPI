<?php

/**
 * Testing RegExp 
 */
require 'api-autoloader.php';

// debug function
function show($data) {
	echo '<pre>' . print_r($data, true) . '</pre>';
}

$start = microtime(true);
$options = array(
	CURLOPT_RETURNTRANSFER => true, // return web page
	CURLOPT_HEADER => false, // return headers
	CURLOPT_FOLLOWLOCATION => false, // follow redirects
	CURLOPT_ENCODING => "", // handle all encodings
	CURLOPT_AUTOREFERER => true, // set referer on redirect
	CURLOPT_CONNECTTIMEOUT => 15, // timeout on connects
	CURLOPT_TIMEOUT => 15, // timeout on response
	CURLOPT_MAXREDIRS => 5, // stop after 10 redirects
	CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36",
	CURLOPT_HTTPHEADER => array('Content-type: text/html; charset=utf-8', 'Accept-Language: en'),
);

$ch = curl_init("http://eu.finalfantasyxiv.com/lodestone/character/9284891/");
curl_setopt_array($ch, $options);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/html; charset=utf-8'));
$source = curl_exec($ch);
curl_close($ch);

$finish = microtime(true);
show("Load HTML (Curl): " . ($finish - $start) . ' ms');


$start = microtime(true);

$p = new \Viion\Lodestone\Parser($source);
$character = new stdClass();
foreach($p->findAll('ic_class_wh24_box', 3, 'base_inner') as $i => $node) {
	$node = new \Viion\Lodestone\Parser($node);
	$name = $node->find('ic_class_wh24_box')->text();

	if($name) {
		$exp = explode(' / ', $node->find('ic_class_wh24_box', 2)->text());

		$character->classjobs[] = [
					'icon' => $node->find('ic_class_wh24_box')->attr("src"),
					'name' => $name,
					'level' => $node->find('ic_class_wh24_box', 1)->numbers(),
					'exp_current' => intval($exp[0]),
					'exp_total' => intval($exp[1]),
		];
	}

	unset($node);
}

$finish = microtime(true);
show("Parse classjobs (viion parser): " . ($finish - $start) . ' ms');


$start = microtime(true);
/*
// Item parse
$regExp = "#item_detail_box.*?ic_reflection_box_64.*?<img.*?src=\"([^\"]+?itemicon[^\"]+)\".*?class=\"item_name.*?>([^<]*?)</h2>.*?class=\"category_name\">([^<]*?)</h3>.*?<a href=\"\/lodestone\/playguide\/db\/item\/([\w\d]+?)\/\".*?class=\"pt3 pb3\">.+?\s([0-9]{1,3})</div>#s";

preg_match_all($regExp, $source, $matches);

array_shift($matches);
$items = array();
foreach($matches[0] as $mkey => $match) {
	$items[$matches[3][$mkey]] = array(
		'name' => utf8_encode($matches[1][$mkey]),
		'icon' => utf8_encode($match),
		'slot' => utf8_encode($matches[2][$mkey]),
		'lodestone' => utf8_encode($matches[3][$mkey]),
		'item_level' => utf8_encode($matches[4][$mkey])
	);
}
$finish = microtime(true);
show("Parse items (regExp): " . ($finish - $start) . ' ms');
*/

$regExp = "#ic_class_wh24_box.*?<img.*?src=\"(.*?)\".*?>([^<]+?)<\/td[^<]*?<td[^>]*?>([\d]+?)<\/td[^<]*?<td[^>]*?>([\d-]+?)[^<\d-]+?([\d-]+?)<\/td#s";

preg_match_all($regExp, $source, $matches);
array_shift($matches);
$classjobs = array();
foreach($matches[0] as $mkey => $match) {
	$classjobs[] = array(
		'icon' => utf8_encode($matches[0][$mkey]),
		'name' => utf8_encode($matches[1][$mkey]),
		'level' => $matches[2][$mkey],
		'exp_current' => $matches[3][$mkey],
		'exp_total' => $matches[4][$mkey],
	);
}
$finish = microtime(true);
show("Parse classjobs (regExp): " . ($finish - $start) . ' ms');
echo "<h2>Viion Parser</h2>";
show($character);
echo "<h2>Regexp</h2>";
show($classjobs);

