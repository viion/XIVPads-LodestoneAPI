<meta charset="UTF-8">
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

# -------------------------------

if (isset($_GET['basic']))
{
    $api->useBasicParsing();
}

$id = 730968;
if (isset($_GET['id']))
{
    $id = $_GET['id'];
}

if (isset($_GET['achievements']))
{
    $data = $api->Search->Achievements($id, isset($_GET['all']) ? true : false );
    show($data->dump());
}

if (isset($_GET['last_achievements']))
{
    $data = $api->Search->Achievements($id, isset($_GET['all']) ? true : false,true );
    show($data);
}
else if (isset($_GET['worldstatus']))
{
	$dc = isset($_GET['dc']) ? trim($_GET['dc']) : null;
	$server = isset($_GET['server']) ? trim($_GET['server']) : null;
    $data = $api->Search->Worldstatus($dc, $server);
    show($data);
}
else if (isset($_GET['freecompany']))
{
    $data = $api->Search->FreeCompany($id, isset($_GET['all']) ? true : false );
    show($data);
}
else if (isset($_GET['linkshell']))
{
    $data = $api->Search->Linkshell($id, isset($_GET['all']) ? true : false );
    show($data);
}
else if (isset($_GET['search']))
{
	$name = isset($_GET['name']) ? trim(urldecode($_GET['name'])) : null;
	$server = isset($_GET['server']) ? trim($_GET['server']) : null;
    $results = isset($_GET['results']) ? true : false;

    $data = $api->Search->Character($name, $server, $results);
    show($data);
}
else if (isset($_GET['news']))
{
	// $_GET['func'] should be Topics, Maintenance, Status, Updates or Notices
	$func = isset($_GET['func']) ? $_GET['func'] : 'Topics';
	$detailHash = isset($_GET['hash']) ? $_GET['hash'] : null;
	$data = $api->Search->{$func}($detailHash);
	show($data);
}
else if (isset($_GET['devtracker']))
{
	$data = $api->Search->Devtracker();
	show($data);
}
else if (isset($_GET['items']))
{
	$ids = (isset($_GET['ids'])) ? explode(',',$_GET['ids']) : null;
	$all = (isset($_GET['all'])) ? true : false;
	$data = $api->Search->ItemDB($all,$ids);
	show($data);
}
else
{
    $data = $api->Search->Character($id);
    if ($api->Search->isMaintenance())
    {
        show('Lodestone is under maintenance');
    }
    else
    {
        show($data->dump());
        show($data->getGearBonus());
    }
}

# -------------------------------

show("Memory: ". cMem(memory_get_usage()) .' - after api->Search->Character');
$finish = microtime(true);

unset($api);
show("Memory: ". cMem(memory_get_usage()) .' - unset API');
show("Duration: ". ($finish - $start) .' ms');
show("Memory Peak: ". cMem(memory_get_peak_usage()));
show("Memory: ". cMem(memory_get_usage()) .' - end');