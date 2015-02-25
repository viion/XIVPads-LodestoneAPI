<?php
$start = microtime(true);

// debug function
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }

// require auto loader
//require 'vendor/autoload.php';
require 'api-autoloader.php';
use Viion\Lodestone\LodestoneAPI;

// new API
$api = new LodestoneAPI();

// Get character

//    $character = $api->Search->Character('Premium Virtue', 'Hyperion');
    $character = $api->Search->Character(730968);
    show($character->dump());

    //$worldStatus = $api->Search->Worldstatus('Chaos','Zodiark');
    //show($worldStatus);

/*
// Get achievements
$achievements = $api->Search->Achievements(730968, true);
show($achievements->dump());
*/
$finish = microtime(true);

show("Duration: ". ($finish - $start) .' ms');
show("Memory: ". memory_get_peak_usage(true));