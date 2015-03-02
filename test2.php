<?php


$start = microtime(true);
show("Memory: ". cMem(memory_get_usage()) .' - start');

// debug function
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }
function cMem($size) { $tmp = array('b','kb','mb','gb','tb','pb'); return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$tmp[$i]; }

// require auto loader
//require 'vendor/autoload.php';
show("Memory: ". cMem(memory_get_usage()) .' - before autoloader');
require 'api-autoloader.php';
use Viion\Lodestone\LodestoneAPI;
show("Memory: ". cMem(memory_get_usage()) .' - after autoloader');

// new API
$api = new LodestoneAPI();
show("Memory: ". cMem(memory_get_usage()) .' - after new api instance');

// Get character

//    $character = $api->Search->Character('Premium Virtue', 'Hyperion');
    $id = 730968;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    }
    $character = $api->Search->Character($id);
    show($character->dump());

    //$worldStatus = $api->Search->Worldstatus('Chaos','Zodiark');
    //show($worldStatus);

/*
// Get achievements
$achievements = $api->Search->Achievements(730968, true);
show($achievements->dump());
*/
show("Memory: ". cMem(memory_get_usage()) .' - after api->Search->Character');
$finish = microtime(true);

unset($api);
show("Memory: ". cMem(memory_get_usage()) .' - unset API');

show("Duration: ". ($finish - $start) .' ms');
show("Memory Peak: ". cMem(memory_get_peak_usage()));

show("Memory: ". cMem(memory_get_usage()) .' - end');