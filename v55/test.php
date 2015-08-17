<meta charset="UTF-8">
<?php
// ------------------------------------------------------------
// Test stuff
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }
// ------------------------------------------------------------

// Only include this if you are not using composers existing auto loader.
require 'api-autoloader.php';

// Use the API
use Viion\Lodestone\LodestoneAPI;
$api = new LodestoneAPI();

// Search for an item
$item = $api->PlayGuide->Items->find('hive');


show($item);


// show log
$api->showLog();