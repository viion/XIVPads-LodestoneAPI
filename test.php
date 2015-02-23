<?php
// debug function
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }

// require auto loader
require 'api-autoloader.php';

// new API
$api = new Viion\Lodestone\LodestoneAPI();

// Get character
$character = $api->Search->Character(730968);

// debug
show('Complete');
show($character);