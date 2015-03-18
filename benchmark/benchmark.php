<?php ini_set('max_execution_time', 300); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<style>
html, body { font-family: Arial; font-size: 13px; }
.box { display: inline-block; padding:8px; background-color: #eee; }
.error { background-color: #f00; color: #fff; }
canvas { margin: 20px; padding: 20px; border: solid 2px #1285EA; border-radius: 3px; }
</style>
<?php
$idList = [
34725,34761,34849,34956,35217,35222,35265,35270,35299,35304,35421,35574,35589,35726,35889,36178,36277,36284,36363,36374,36402,
36476,36552,36557,36654,36663,36722,36749,36752,37054,37062,37089,37094,37122,37137,37333,37340,37375,37396,37444,37644,37813,
37958,38094,38351,38395,38443,38502,38911,38952,38987,39021,39048,39071,39142,39175,39255,39313,39381,39742,39831,40035,40069,
40211,40223,40369,40535,40945,41423,41489,41593,41762,41864,41885,41904,41953,42093,42216,42396,42659,42934,43033,43046,43057,
43067,43085,43412,44308,44443,44802,44955,44969,45106,45221,45281,45300,45476,45661,45670,45820,45924,45953,46115,46131,46226,
46264,46482,46529,46538,46941,46954,47099,47341,47475,47601,47709,47717,47846,47885,47917,48026,48089,48127,48143,48286,48312,
48506,48567,48601,48603,49102,49174,49667,49677,49805,49831,50188,50543,50581,50627,50633,50672,50943,50950,50978,50981,50984,
51003,51039,51303,51345,51475,51587,51599,51603,51655,51683,51732,51762,52324,52348,52456,52459,52521,52540,53115,53180,53532,
53551,53557,53655,53917,54049,54413,54497,54570,54825,54885,54938,54984,55086,55093,55364,55412,55632,55709,56182,56284,56342,
56467,56772,56964,57003,57090,57118,57307,57315,57450,57564,57836,57968,58294,58410,58472,58487,58566,58687,58751,59027,59058,
59259,59403,59624,59887,59927,60061,60199,60576,60626,60646,60802,61506,61512,61531,61610,61700,61967,62211,62397,62605,62940,
62946,63073,63780,63963,64277,64453,64589,64630,64670,64793,65267,65310,65598,65709,65751,65815,65932,66012,66045,66176,66188,
66286,66537,66899,67289,67443,67846,68120,68249,68964,69185,69516,69611,69881,69934,70302,70661,70673,70764,70807,70843,71128,
72100,72243,72270,72319,72456,72595,72640,72979,72981,73089,73174,73257,73279,73289,73410,73495,73497,73725,74454,74914,75391,
76171,76407,76653,77119,77385,77578,77614,77641,77645,77739,78062,78080,78349,78391,78404,79340,79576,80151,80324,80524,80635,
80678,80835,80920,81104,81430,81557,81572,81614,81648,81850,81880,81884,82061,82068,82112,82453,82469,82569,82714,82724,82975,
82984,83104,83194,83309,83542,83546,83580,83652,83925,84003,84133,84234,84252,84368,84463,84856,85009,85131,85269,85411,85712,
85944,85981,86056,86076,86141,86325,86714,86760,86990,87063,87070,87153,87162,87233,87283,87327,87458,87607,87774,88150,88342,
88666,88784,88916,89234,89319,89325,89420,89473,89499,89953,89996,90238,90544,90739,90747,90791,90800,90957,91283,91321,91467,
91551,91634,91723,92006,92011,92154,92437,92450,92551,92768,92884,92985,93291,93396,93410,93839,93945,94045,94209,94533,94540,
94541,94549,94560,95585,95622,95691,95729,95804,95900,96020,96061,96072,96156,96424,96646,96764,96864,97246,97458,97481,97636,
97864,97958,98168,98205,98458,98531,98565,98647,98754,98825,98831,98850,98856,98901,99133,99526,99566,99576,99631,99762,99798,
99878,100133,100214,100409,100454,100506,100508,100621,100638,100842,100849,101060,101104,101254,101350,101485,101649,101825,
101841,101924,101949,101985,102098,102138,102203,102275,102479,102552,102602,102694,102811,102825,102858,102869,102980,103032,
103092,103493];

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
function cMem($size, $type = false) {
    $tmp = array('b','kb','mb','gb','tb','pb');
    $v = @round($size/pow(1024,($i=floor(log($size,1024)))),2);
    if ($type) {
        $v .= ' '. $tmp[$i];
    } else {
        if ($tmp[$i] == 'kb') {
            $v = '0.'. ceil($v);
        }
    }

    return $v;
}
function aMem($key) { global $memory; $memory[$key] = [ cMem(memory_get_usage(), true), cMem(memory_get_usage(true), true) ]; }

// Turn off buffering
ob_end_flush();

// log
$memory = [];
$log = [ 'mem' => [], 'memInc' => [], 'times' => [] ];
$allStart = microtime(true);

aMem('start');

// get api
require '../api-autoloader.php';
aMem('require autoloader');

use Viion\Lodestone\LodestoneAPI;
aMem('use viion - lodestone - api');

// new API
$api = new LodestoneAPI();
aMem('api = new api');

// ----------------------
// Size and List
// ----------------------

//$idList = explode("\n", file_get_contents(__DIR__ .'/idlist.txt'));
$benchmarkSize = isset($_GET['size']) ? intval(trim($_GET['size'])) : 3;
aMem('got benchmark id list');

shuffle($idList);
aMem('shuffle list');

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
aMem('before benchmark');
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
aMem('finished benchmark');

$allfinish = microtime(true);
$allduration = ($allfinish - $allStart);
unset($api);

aMem('end');

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
echo '<h1>Global memory usage</h1>';
show($memory);

echo '<h1>Log</h1>';
show($log);
?>