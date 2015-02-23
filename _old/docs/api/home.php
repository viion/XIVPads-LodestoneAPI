<h1>home</h1>

<p>The LodestoneAPI is, an API to parse Lodestone!</p>

<p>This is a PHP API and has been included into 1 big file for ease of use. You can find the official <strong>GitHub</strong> 
page at: <a href="https://github.com/viion/XIVPads-LodestoneAPI" target="_blank">https://github.com/viion/XIVPads-LodestoneAPI</a>.</p>

<div class="docspacer"></div>

<h5>Requirements</h5>
<ul>
    <li>
        <h6 class="orange">PHP 5.4</h6>
        <p>Unfortunately this is not going to change, this is a decision I've made while working with the API. PHP 5.4 has been around for a while now and I believe
        it is time for things to move forward. The reasoning for 5.4 is the use of <a href="http://php.net/manual/en/migration54.new-features.php" target="_blank">Function 
        array dereferencing</a> and <a href="http://php.net/manual/en/language.oop5.traits.php" target="_blank">Traits</a>.</p>
    </li>
    <li>
        <h6 class="orange">CURL Enabled</h6>
        <p>This is an absolute requirement to parse the site. You can check if this is on by looking at your <a href="http://php.net/manual/en/function.phpinfo.php" target="_blank">phpinfo()</a>.</p>
    </li>
</ul>

<script src="https://gist.github.com/viion/efef810dcb7f456a8a64.js"></script>

<div class="docline"></div>

<h5>Programming Style</h5>
<ul>
    <li>
        <h6 class="orange">Object-Oriented Programming</h6>
        <p>The majority of the API is based around the principle of objects, with the exception of Social feeds as these are usually stored into a json or rss files. This means
        when you parse <strong>a character</strong> or <strong>multiple characters</strong> you will always be given a "object" back (or multiple objects), that has various 
        functions and the data for the character, linkshell or FC.</p>
    </li>
</ul>

<div class="docline"></div>

<h5>General Notes</h5>
<ul>
    <li>
        <h6 class="orange">Issues/Bugs/Problems</h6>
        <p>If you have an issue, please create an issue on GitHub, this allows me to properly collect, organize and react to any bugs or problems as well as providing
        access to others to see incase they experience the same issue. Please do this here: <a href="https://github.com/viion/XIVPads-LodestoneAPI/issues?state=open" target="_blank">
        https://github.com/viion/XIVPads-LodestoneAPI/issues?state=open</a>.</p>
    </li>
    <li>
        <h6 class="orange">Tab Indent</h6>
        <p>The Tab Indent is <strong>4 Spaces</strong>, you can change your IDE to this to make it look good, if you work on the API please use the same</p>
    </li>
    <li>
        <h6 class="orange">The Documentation</h6>
        <p>These docs are only meant to be guide lines to get you started, setting up the best method for parsing and storing is up to you.
        I will only be detailing public variables and functions.</p>
    </li>
</ul>

<div class="docline"></div>

<h5>Other Resources</h5>
<ul>
    <li>
        <h6 class="orange">Item to XIVDB:ID</h6>
        <p>If you wish to have XIVDB id's in the parse (thus easy to make tooltips), upload <a href="http://xivpads.com/items.json" target="_blank">items.json</a> to the same location
        as the API PHP file.</p>
    </li>
    <li>
        <h6 class="orange">Lodestone RSS</h6>
        <p>If you'd like the latest topics that are posted on Lodestone, you can parse: <a href="http://xivpads.com/lodestone_rss.json" target="_blank">lodestone_rss.json</a>.</p>
    </li>
    <li>
        <h6 class="orange">Social Json dump</h6>
        <p>If you'd like a json dump of a whole bunch of social activity (Same as XIVDB's front page), you can parse: <a href="http://xivpads.com/resources/devjson/data.json" target="_blank">devjson/data.json</a>.</p>
    </li>
</ul>

<div class="notice">
    <p>When reading links from XIVPads, I highly recommend using an automatic storage for the file either using something like Memcached or storing to a DB on a cronjob, or at minimum writing to a file</p>
</div>

<div class="docline"></div>

<h5>Thanks to:</h5>
<ul>
    <li><h6 class="blue">@eedev</h6> <p>Additional gear data, composer and general debugging</p></li>
    <li><h6 class="blue">@stygiansabyss</h6> <p>Patch 2.1 FC fixes</p></li>
</ul>

<div class="docline"></div>

<h5>License</h5>
<p><strong>MIT License : Copyright (c) 2014 Josh Freeman</strong><p>

<p>
You may: use, redistribute, modify, share, collaborate, change spaces to tabs, so long as the comment license stays intack at the top. 
IF YOU MAKE MODIFICATIONS please add your contribution details (name/git handle) in the heading comment, example: http://phpjs.org/functions/number_format/
</p>