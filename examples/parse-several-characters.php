<?php
/**
 * This shows an example of parsing several characters
 *
 *
 *
 *
 */

// Include API
require __DIR__ .'/../api-autoloader.php';

// Use the API
use Viion\Lodestone\LodestoneAPI;

// New API
$api = new LodestoneAPI();

// List of IDs
$array = [730968, 9284891, 8805967];

// The array we will store them to
$characters = [];

// Get th edata
foreach($array as $id) {
    $characters[] = $api->Search->Character($id);
}

// Output
foreach($characters as $character) {
    echo $character->name .'<br>';
}