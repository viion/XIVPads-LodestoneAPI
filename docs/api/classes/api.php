<h1><i class="fa fa-code"></i> Class: API</h1>

<div class="classblock">
    <p><strong>namespace</strong> Viion/Lodestone</p>
    <p><strong>extends</strong> Parser</p>
    <p><strong>traits</strong> Funky, Config</p>
</div>

<div class="docspacer"></div>

<h4>Description</h4>
<p>This is the core area of the API and where you will do calls to <strong>fetch</strong> data.</p>
<script src="https://gist.github.com/viion/8e419153efe929c1216e.js"></script>

<!-- Variables -->
<div class="docline"></div>
<h4>Variables</h4>
<table class="doctable" cellspacing="0" cellpadding="0" border="0">
<tr class="header">
    <td>parameter</td>
    <td>type</td>
    <td>description</td>
</tr>
<?php
$table =
[
    [ 'AchievementCategories', 'array', 'Array containing a list of achievement categories.' ],
    [ 'ClassList', 'array', '...' ],
    [ 'ClassDisicpline', 'array', '...' ],
    [ 'GearSlots', 'array', 'Array containing gear slots (as strings),' ],

    [ 'Characters', 'array', 'Array containing Character objects for any characters searched.' ],
    [ 'Achievements', 'array', 'Array containing character achievements, the list datatype is Achievement object' ],
    [ 'Search', 'array', 'Array containing the results of any current search' ],

    [ 'FreeCompanyList', 'array', 'Array containing Freecompany objects for any FCs searched.' ],
    [ 'FreeCompanyMembersList', 'array', 'Array containing characters to an FC.' ],

    [ 'Linkshells', 'array', 'Array containing Linkshell objects for any LSs searched.' ],
];

printTable($table);
?>
</table>

<!-- Functions -->
<div class="docline"></div>
<h4>Functions</h4>
<table class="doctable" cellspacing="0" cellpadding="0" border="0">
<tr class="header">
    <td>function</td>
    <td>return</td>
    <td>description / code gist</td>
</tr>
<?php
$table =
[
    [ '__construct()', '-', '...', ],

    // Quick functions
    'Quick Functions <span style="opacity:0.5;">*recommended</span>',
    [ 'get()', '(object) Character', 'Get a Character object. Pass in an array containing either Name/Server OR id. <script src="https://gist.github.com/viion/ee039dffea7d43b59e1a.js"></script>', ],
    [ 'getFC()', '(object) FreeCompany', 'Get a FreeCompany object. Pass in an array containing either Name/Server OR id. An $Options param can be parsed to state whether to fetch members or not. <script src="https://gist.github.com/viion/a3c884a133b408b88a3a.js"></script>', ],
    [ 'getLS()', '(object) Linkshell', 'Get a Linkshell object. Pass in an array containing either Name/Server OR id. <script src="https://gist.github.com/viion/b6152fe131d12a84d865.js"></script>', ],

    // Access functions
    'Access Functions',
    [ 'Lodestone()', '(object) Lodestone', 'Get access to the Lodestone class, for parsing specific stuff off the Lodestone Website. <script src="https://gist.github.com/viion/be3d73d69e687f1bd9b3.js"></script>', ],
    [ 'Social()', '(object) Social', 'Get access to the Social class, for parsing Twitter and Youtube. <script src="https://gist.github.com/viion/78cf75b00a8853ba98a0.js"></script>', ],

    // Search
    'Search',
    [ 'searchCharacter()', '(object) Character', 'Search a Character. <script src="https://gist.github.com/viion/44113b3a20e4f6a1a27b.js"></script>', ],
    [ 'searchFreeCompany()', '(object) Freecompany', 'Search a Free Company. <script src="https://gist.github.com/viion/a5cf91ff0f94c78d69e0.js"></script>', ],
    [ 'searchLinkshell()', '(object) Linkshell', 'Search a Linkshell. <script src="https://gist.github.com/viion/bf5f4b7e531bb91bcbac.js"></script>', ],

    // Search return
    'Search return',
    [ 'getSearch()', 'array', 'Get an array containing the latest search results. This is used in the previous 3 search<Type>() code examples above.', ],
    [ 'errorPage()', 'boolean', 'This function is not recommended, a simple conditional check is more practical and readable: <script src="https://gist.github.com/viion/bccd377ac077cf43cd83.js"></script>', ],

    // Character
    'Character',
    [ 'parseProfile()', '-', 'Parse a characters profile via their lodestone ID. <script src="https://gist.github.com/viion/0ebcca0c48b89f584ab6.js"></script>', ],
    [ 'parseBiography()', '-', 'Parse a characters biography via their lodestone ID. <script src="https://gist.github.com/viion/c8611e5d9e56cbd5811a.js"></script>', ],
    [ 'getCharacters()', 'array of (object) Character', 'Get an array containing Character objects for all parsed Characters.', ],
    [ 'getCharacterByID()', '(object) Character', 'Get a Character object via its ID.', ],

    // Achievements
    'Achievements',
    [ 'parseAchievements()', '-', 'Parse a characters achievements via their lodestone ID', ],
    [ 'parseAchievementsByCategory()', '-', 'Parse an achievements catagory of a character via their lodestone ID', ],
    [ 'getAchievements()', 'array of (object) Achievement', 'Get an array containing Character objects for all parsed Characters.', ],
    [ 'getAchievementCategories()', '(object) Achievement', 'Get a list of achievement categories (an array of strings)', ],

    // Freecompany
    'FreeCompany',
    [ 'parseFreeCompany()', '-', 'Parse a FreeCompany via its lodestone ID', ],
    [ 'getFreeCompanies()', 'array of (object) FreeCompany', 'Gets an array containing FreeCompany objects for all parsed Free Companies.', ],
    [ 'getFreeCompanyByID()', '(object) FreeCompany', 'Gets a FreeCompany object via its ID.', ],

    // Linkshell
    'Linkshell',
    [ 'parseLinkshell()', '-', 'Parse a Linkshell via its lodestone ID', ],
    [ 'getLinkshells()', 'array of (object) Linkshell', 'Gets an array containing Linkshell objects for all parsed Linkshells.', ],
    [ 'getLinkshellByID()', '(object) Linkshell', 'Gets a Linkshell object via its ID.', ],
    
    
];

printTable($table);
?>
</table>