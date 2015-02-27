<meta charset="UTF-8">
<?php


// debug function
function show($data) {
	echo '<pre>' . print_r($data, true) . '</pre>';
}


function oldConstruct($html)
{
	if (is_array($html)) {
		$html = implode("\n", $html);
		$html = html_entity_decode($html);
	}

	$html = htmlentities($html);
	$html = explode("\n", $html);

	foreach($html as $i => $h) {
		$h = preg_replace('/\s+/', ' ', $h);
		$h = trim($h);

		// skip some stuff
		if (
			substr(html_entity_decode($h), 0, 2) == '</' ||
			substr(html_entity_decode($h), 0, 4) == '<!--'
		) {
			unset($html[$i]);
			continue;
		}

		$html[$i] = trim($h);
	}

	$html = array_values(array_filter($html));

	return $html;
}
function newConstruct($html)
{
	// Building cleaned up on line html
	$html = preg_replace(array('#\s\s+#s','#<!--.*?-->#s','#<script.*?>.*?<\/script>?#s','#[\n\t]#s'),'', $html);
	/*
	// Split
	$htmlArray = preg_split('#(<\/?.*?>)#',$html,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
	// Remove closingtags
	foreach($htmlArray as $key => $value){
		if(preg_match('#<(\/?[^\s]+?)>#',$value)){
			unset($htmlArray[$key]);
		}
	}
	 $return = array_values($htmlArray);
	 */
	$return = $html;
	return $return;
}

function arrayEntities(&$value,$key){
	$value = htmlentities($value);
}

function trimHTML($html, $start, $end) {
	$temp = $html;

	// Start position
	$start = strpos($temp, $start);

	// cut to start
	$temp = substr($html, $start);

	// Cut to end
	$end = strpos($temp, $end) + strlen($end);

	// sub from entire
	$html = substr($html, $start, $end);

	return $html;
}

/**
 * Testing RegExp 
 */
$start = microtime(true);
require 'api-autoloader.php';
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

$source = trimHTML($source, '<!-- contents -->', '<!-- //Minion -->');

$finish = microtime(true);
show("Load HTML (Curl): " . ($finish - $start) . ' ms');


// Old Construct
$start = microtime(true);
$old = oldConstruct($source);
$finish = microtime(true);
show("old constructor: " . ($finish - $start) . ' ms');


// New Construct
$start = microtime(true);
$new = newConstruct($source);
$finish = microtime(true);
show("new constructor: " . ($finish - $start) . ' ms');
echo "HTML:<hr />";
$newShow = htmlentities($new);
show($newShow);

$html = $new;


// parse stats
$base = array();
$items = array();
$classjobs = array();
$attributes = array();

$startAll = microtime(true);

// Base Data
$start = microtime(true);
$regExp = "#player_name_thumb.*?src=\"(?<avatar>.*?)\".*?"
		. "<a.*?>(?<name>.*?)<\/a>"
		. "<span>\s*?\((?<server>.*?)\).*?"
		. "<div class=\"chara_title\">(?<title>.*?)</div>.*?"
		. "txt_selfintroduction\">(?<bio>.*?)<\/div>.*?"
		. "chara_profile_title\">(?<raceClanGender>.*?)</div>.*?"
		. "txt_name\">(?<nameday>.*?)<\/dd>.*?"
		. "txt_name\">(?<Guardian>.*?)<\/dd>.*?"
		. "txt_name\">(?<city>.*?)<\/dd>.*?"
		. "txt_name\">(?<grandcompany>.*?)<\/dd>.*?"
		. "ic_crest_32.*?src=\"(?<freecompanyIcon1>.*?)\".*?src=\"(?<freecompanyIcon2>.*?)\".*?src=\"(?<freecompanyIcon3>.*?)\".*?"
		. "txt_name\">.*?href=\".*?\/(?<freecompanyid>[\d]+?)\/\".*?>(?<freecompany>.*?)<\/a>.*?"
		. "#";

if(preg_match($regExp, $html, $matches)){
	array_shift($matches);
	$base = $matches;
}
$finish = microtime(true);
show("Parse Base: " . ($finish - $start) . ' ms');

// attributes
$start = microtime(true);
$attrHtml = trimHTML($html, 'param_left_area', 'param_power_area');
$regExp = "#li class=\"(?<attr>.*?)(?:\s?clearfix)?\">(?<content>.*?)<\/li#";

preg_match_all($regExp, $attrHtml, $matches, PREG_SET_ORDER);
array_shift($matches);
foreach($matches as $mkey => $match) {
	array_shift($match);
	$key = strtolower(str_ireplace(' ', '-', $match['attr']));
	$value = $match['content'];
	if($match['attr'] == "") {
		preg_match('#<span class="left">(?<key>.*?)</span><span class="right">(?<value>.*?)</span>#', $match['content'], $tmpMatch);
		$key = strtolower(str_ireplace(' ', '-', $tmpMatch['key']));
		$value = $tmpMatch['value'];
	}elseif(stripos($match['content'], 'val') !== false) {
		preg_match('#>(?<value>[\d-]*?)</span>#', $match['content'], $tmpMatch);
		$value = $tmpMatch['value'];
	}
	$attributes[$key] = $value;
}
$finish = microtime(true);
show("Parse Attributes: " . ($finish - $start) . ' ms');

// Items
$start = microtime(true);
$regExp = "#item_detail_box.*?ic_reflection_box_64.*?<img.*?src=\"(?<icon>[^\"]+?itemicon[^\"]+)\".*?class=\"item_name.*?>(?<name>[^<]*?)</h2>.*?class=\"category_name\">(?<slot>[^<]*?)</h3>.*?<a href=\"\/lodestone\/playguide\/db\/item\/(?<lodestone>[\w\d]+?)\/\".*?class=\"pt3 pb3\">.+?\s(?<item_level>[0-9]{1,3})</div>#";

preg_match_all($regExp, $html, $matches, PREG_SET_ORDER);

array_shift($matches);
foreach($matches as $mkey => $match) {
	array_shift($match);
	$items[] = $match;
}
$finish = microtime(true);
show("Parse Items: " . ($finish - $start) . ' ms');

// Classjobs

$start = microtime(true);
$regExp = "#ic_class_wh24_box.*?<img.*?src=\"(?<icon>.*?)\".*?>(?<name>[^<]+?)<\/td><td[^>]*?>(?<level>[\d-]+?)<\/td><td[^>]*?>(?<exp_current>[\d-]+?)\s\/\s(?<exp_total>[\d-]+?)<\/td#";

preg_match_all($regExp, $html, $matches, PREG_SET_ORDER);
array_shift($matches);
foreach($matches as $mkey => $match) {
	array_shift($match);
	$classjobs[] = $match;
}
$finish = microtime(true);
show("Parse Classjobs: " . ($finish - $start) . ' ms');


$finishAll = microtime(true);
show("Parse overall: " . ($finishAll - $startAll) . ' ms');
//echo "<h2>Viion Parser</h2>";
//show($character);
echo "<h2>Regexp</h2>";
show(array('base' => $base,'items' => $items, "classjobs" => $classjobs, 'attributes' => $attributes,));
