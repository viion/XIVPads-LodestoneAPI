<meta charset="UTF-8">

<?php
$start = microtime(true);


//----------------------------------------
include "api.php";
include "apilogger.php";
$API = new LodestoneAPI();
$API_Logger = new LodestoneLogger();
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }
//----------------------------------------


// Test achievements
$id = 730968;
$API = new LodestoneAPI();
$Entity = $API->get([ 'id' => $id ]);

show($Entity->datadump());

$finish = microtime(true);

show("Duration: ". ($finish - $start) .' ms');
show("Memory: ". memory_get_peak_usage(true));