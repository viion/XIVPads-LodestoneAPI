<?php
$start = microtime(true);

// debug function
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }

// require auto loader
require 'api-autoloader.php';

// new API
$api = new Viion\Lodestone\LodestoneAPI();

// Get character

//    $character = $api->Search->Character('Premium Virtue', 'Hyperion');
    $character = $api->Search->Character(730968);
    show($character->dump());

/*
// Get achievements
$achievements = $api->Search->Achievements(730968, true);
show($achievements->dump());
*/
$finish = microtime(true);

show("Duration: ". ($finish - $start) .' ms');
show("Memory: ". memory_get_peak_usage(true));