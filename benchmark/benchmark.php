<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<style>
html, body { font-family: Arial; font-size: 13px; }
.box { display: inline-block; padding:8px; background-color: #eee; }
.error { background-color: #f00; color: #fff; }
canvas { margin: 20px; padding: 20px; border: solid 2px #1285EA; border-radius: 3px; }
</style>
<?php
/**
 * The purpose of this script is to run the API over a large number
 * of characters and output some results.
 *
 * The results will consist of validating the return, error checking, times and memory.
 *
 * Flush will be used as expected
 */

// debug function
function show($data) { echo '<pre>'. print_r($data, true) .'</pre>'; }
function cMem($size) { $tmp = array('b','kb','mb','gb','tb','pb'); return @round($size/pow(1024,($i=floor(log($size,1024)))),2); }

// Turn off buffering
ob_end_flush();

// log
$log = [ 'mem' => [], 'memInc' => [], 'times' => [] ];
$allStart = microtime(true);

// get api
require '../api-autoloader.php';
use Viion\Lodestone\LodestoneAPI;

// new API
$api = new LodestoneAPI();

// ----------------------
// Size and List
// ----------------------

$idList = explode("\n", file_get_contents(__DIR__ .'/idlist.txt'));
$benchmarkSize = isset($_GET['size']) ? intval(trim($_GET['size'])) : 3;

shuffle($idList);

$graphWidth = 1000;
if ($benchmarkSize > 300) {
    $graphWidth = 2000;
}

// ----------------------
// Start
// ----------------------

// run
$success = 0;
$errors = 0;
foreach($idList as $i => $id)
{
    if ($i == $benchmarkSize) {
        break;
    }

    $id = intval(trim($id));
    $start = microtime(true);

    // get character
    $character = $api->Search->Character($id);

    // verify name
    if ($character && $character->name)
    {
        $success++;
        echo '<span class="box">['. $i .'] '. $id .'-'. $character->name .'</span> ';
    }
    else
    {
        $errors++;
        echo '<span class="box error">['. $i .'] Failed: '. $id .'</span> ';
    }


    // Stats
    $log['mem'][] = cMem(memory_get_usage());
    $log['memInc'][] = cMem(memory_get_usage(true));
    $log['times'][] = round(microtime(true) - $start, 6);

    // flush!!
    flush();
}

$allfinish = microtime(true);
$allduration = ($allfinish - $allStart);
unset($api);

// ----------------------
// fin
// ----------------------

show('Parsed '. $benchmarkSize .' characters');
show('There were: '. $success .' successful parses');
show('there were: '. $errors .' failed parses');
show('Start: '. $allStart);
show('Finish: '. $allfinish);
show('Duration: '. $allduration);

// ----------------------
// New API
// ----------------------

$timeslabels = '"'. implode('", "', array_keys($log['times'])) .'"';
$timesdata = implode(',', ($log['times']));
$memlabels = '"'. implode('", "', array_keys($log['mem'])) .'"';
$memdata = implode(',', ($log['mem']));
$memInclabels = '"'. implode('", "', array_keys($log['memInc'])) .'"';
$memIncdata = implode(',', ($log['memInc']));

// ----------------------
// Graphs
// ----------------------

?>
<div>
    <h1>Times</h1>
    <canvas id="times" width="<?=$graphWidth;?>px" height="300"></canvas>
    <script>
    var data = {
        labels: [<?=$timeslabels?>],
        datasets: [
            {
                label: "Times",
                fillColor: "rgba(32,124,202,0.2)",
                strokeColor: "rgba(32,124,202,1)",
                pointColor: "rgba(32,124,202,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(32,124,202,1)",
                data: [<?=$timesdata?>]
            },
        ]
    };

    var ctx = document.getElementById("times").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data);
    </script>
</div>

<div>
    <h1>Memory</h1>
    <canvas id="mem" width="<?=$graphWidth;?>px" height="300"></canvas>
    <script>
    var data = {
        labels: [<?=$memlabels?>],
        datasets: [
            {
                label: "Memory",
                fillColor: "rgba(32,124,202,0.2)",
                strokeColor: "rgba(32,124,202,1)",
                pointColor: "rgba(32,124,202,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(32,124,202,1)",
                data: [<?=$memdata?>]
            },
        ]
    };

    var ctx = document.getElementById("mem").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data);
    </script>
</div>

<div>
    <h1>Memory Peak</h1>
    <canvas id="memInc" width="<?=$graphWidth;?>px" height="300"></canvas>
    <script>
    var data = {
        labels: [<?=$memInclabels?>],
        datasets: [
            {
                label: "Memory",
                fillColor: "rgba(32,124,202,0.2)",
                strokeColor: "rgba(32,124,202,1)",
                pointColor: "rgba(32,124,202,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(32,124,202,1)",
                data: [<?=$memIncdata?>]
            },
        ]
    };

    var ctx = document.getElementById("memInc").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data);
    </script>
</div>


<?php
show($log);
?>