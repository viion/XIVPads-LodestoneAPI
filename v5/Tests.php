<?php
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }
require __DIR__.'/LodestoneAPI.php';

// Initialize API
$API = new Viion\Lodestone\LodestoneAPI();
$API->setOutputToUTF8();
// ------------------------------------------------------------------


$API->Search->Character('Premium Virtue', 'Hyperion');



// ------------------------------------------------------------------
echo '<hr><h1>Log</h1>';
$API->Log->show();