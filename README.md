XIVPads-LodestoneAPI
====================
An API for parsing lodestone data, designed and maintained by XIVPads.com and XIVDB.com

- For help and code examples on XIVDB's Tooltips, go to: https://github.com/viion/XIVDB-Tooltips
- For an Lodestone RSS Json file, view: https://github.com/viion/XIVPads-LodestoneAPI/blob/master/lodestone_rss.md

**Requirements**
- PHP 5.4 (Function array dereferencing http://php.net/manual/en/migration54.new-features.php)
- CURL Enabled on your Server (use phpinfo() to check this)
- Moderate understanding of Object-Oriented Programming
- 500 Gil

**General Notes**
- If you have problems, please create an issue on here.
- The tab indent size is "4 spaces", if you set your IDE to this it will look nice.

**Implemented**
- Character Search
- Profile Parse
- Achievement Parse
- Free Company Search/Parse
- Linkshell Search/Parse
- Lodestone news parse

**Todo (order of priority)**
- Clean up some code (24/7!)
- Lodestone Database Parse
- Friend Parse
- Blog Parse

**Thanks:**
- @JohnRamboTSQ for adding: Big Portrait and Gender detection!, + Blog and World Status!!! Helping migrate to phpQuery
- @FlyerUA for bug detection and additional functionality: probableJobs()
- @eedev for (Gear level, ID Lodestone and "Cannot equip gear to" data on the gear array) https://github.com/viion/XIVPads-LodestoneAPI/pull/32
- @stygiansabyss for patch 2.1 free company icon fix

**License**
- MIT License : Copyright (c) 2014 Josh Freeman
- You may: use, redistribute, modify, share, collaborate, change spaces to tabs, so long as the comment license stays intack at the top. IF YOU MAKE MODIFICATIONS please add your contribution details (name/git handle) in the heading comment, example: http://phpjs.org/functions/number_format/

Getting Started
--------
The API is heavily object-oriented. It will parse the Lodestone for information and attach this to a 
entity type object, for example searching a character and parsing the profile will create a character
object pre-filled with the profile information where you can call traditional "get" functions.

**IMPORTANT**
Please read this commit: https://github.com/viion/XIVPads-LodestoneAPI/commit/d317664c97ec200d1447da6bcdfe5b730e613127

**Quick Parse**
```php
// Initialize a LodestoneAPI Object
$API = new LodestoneAPI();

// Parse the character: Premium Virtue - Gungnir
$Character = $API->get(array(
  "name" => "Premium Virtue", 
  "server" => "Gungnir"
));

// Print character portrait
echo '<img src="'. $Character->getPortrait() .'" />';
```

**Search Character**
```php
// Search the character: Premium Virtue - Gungnir, with exact matching
$API->searchCharacter("Premium Virtue", "Gungnir", true);

/* The getSearch object will contain an array with the following data:
 * 'total' - The total number of results found
 * 'results' - The list of results found
 * If the 3rd param "Exact" is true, it will attempt to find an exact match
 * if one is found, it will be the only result at index [0] of 'results'
 */
Show($API->getSearch());
```

**Parse by ID**
```php
// Set an ID
$ID = 730968;

// Parse profile
$API->parseProfile($ID);

// Select character
$Character = $API->getCharacterByID($ID);
```

**Parse multiple IDs**

IMPORTANT: Depending on your servers distance from Japan and the bandwidth speed, parsing multiple
characters at the same time can lock down your server and it may crash. I recommend using a buffer
or flush to stock the collected data

```php
// Set an ID
$IDList = array(730968, 123456, 098765)

// Loop through ID list and parse all characters
foreach($IDList as $ID)
{
  // Parse profile
  $API->parseProfile($ID);
}

// Print all character objects
Show($API->getCharacters());

// Select a specific character (in this case, the $IDList index 0)
$Character = $API->getCharacterByID($IDList[0]);
```

**Parse Achievements**

This will take a significant amount of time to complete as it is looping through all achievement 
categories.

```php
// Set an ID
$API = new LodestoneAPI();

// Parse achievements
$API->parseAchievements(730968);

Show($API->getAchievements());
```
You can parse a specific category by doing:
```php
// Lodestone
$API = new LodestoneAPI();

// Set category id
$CategoryID = 2;

// Parse achievement by category
$API->parseAchievementsByCategory($CategoryID, 730968);

// Get achievements
Show($API->getAchievements()[$CategoryID]);
```


**Parse Free Company**

Parse a free company works the same way as characters. The functions to fetch this data allow an config array to be passed with the settings you wish to use. Currently only 1 setting added, this is because the larger free company you parse, the longer it will take and you may wish to not parse members, so by default full member information is not fetched.

***Options***
- "members" [true, false] - default false.

```php
$FreeCompany = $API->getFC(
[
	"name" 		=> "call for help", 
	"server" 	=> "excalibur"
],
[
	"members"	=> true,
]);
Show($FreeCompany); // returned object

// echo general info
echo $FreeCompany->getName();
echo $FreeCompany->getSlogan();

// Members List
Show($FreeCompany->getMembers());
```

The options for getFC are optional, the example below will parse free company but not fetch members.

```php
$FreeCompany = $API->getFC(
[
	"name" 		=> "call for help", 
	"server" 	=> "excalibur"
]);
Show($FreeCompany); // returned object
```

**Parse Linkshell**

Parse a linkshell works the same way as characters/FC. 

```php
$Linkshell = $API->getLS(
[
	"name" 		=> "derp squad", 
	"server" 	=> "excalibur"
]);
Show($Linkshell); // returned object

// echo general info
echo $Linkshell->getName();
echo $Linkshell->getServer();

// Members List
Show($Linkshell->getMembers());
```

API Methods
--------

**LodestoneAPI**
```php
// Quick Get functions
get(array) (array takes: "name" => "xxx", "server" => "xxx", "id" => "123", ID will take priority)
getFC(array, config[optional]) (array takes: "name" => "xxx", "server" => "xxx", "id" => "123", ID will take priority)
getLS(array, config[optional]) (array takes: "name" => "xxx", "server" => "xxx", "id" => "123", ID will take priority)
Lodestone(array) (array takes: topics => true/false)

// Search functions
searchCharacter(name, server, exact[true|false])
searchFreeCompany(name, server, exact[true|false])
SearchLinkshell(name, server, exact[true|false])

// Get Functions
getSearch()
getCharacters()
getCharacterByID(id)
getFreeCompanies()
getFreeCompanyByID(id)
getAchievements()
getLinkshells()
getLinkshellByID(id)

// Parse
parseProfile(id)
parseBiography(id)
parseAchievements()
parseAchievementsByCategory(cID)
parseFreeCompany(id, options[optional])
parseLinkshell(id, options[optiona]) * no options currently

// Error check
errorPage(id)
```

**Lodestone**
```php
getTopics() // an array containing data related to news topics on the Lodestone.
```

**Character**
```php
getID()
getLodestone() // the url of their lodestone profile
getName()
getServer()
getNameClean() // twitter like name, eg "Premium Vir'tue" --> "premiumvirtue"
getAvatar(size) // size of avatars: 50, 64, 96
getPortrait()
getRace()
getClan()
getLegacy() // This is "Sixth Astral Era" status
getNameday()
getGuardian()
getCompanyName()
getCompanyRank()
getFreeCompany() // Returns array with 'name' and 'id'
getCity()
getBiography()
getStat(type, attribute) // (List below)
getGear() // Returns array containing 'name', 'icon', 'slot'
getEquipped(type) // List by 'numbers', or list by 'slots'
getSlot() // (List below)
getActiveClass()
getActiveJob()
getActiveLevel()
getClassJobsOrdered('desc', 'level', 'named'); // Returns class/jobs ordered by an index.
getMinions() // Returns array containing 'Name' and 'Icon'
getMounts() // Returns array containing 'Name' and 'Icon' (Thanks to @Lucleonhart!)
getClassJob(class) // Get level/exp info of a class
isValid() // Wheather character data is valid or not (WIP)
getErrors() // List of errors found during validation
```

**Achievements**
If you parse all achievements via the parseAchievements function, the data below will be an accumlative result. If you use the function parseAchievementsByCategory the results will be specific to that category parsed.
```PHP
get() // Gets the list of achievements in the object
getTotalPoints() // Total points obtainable from parsed result
getCurrentPoints() // Total points the character has obtained for this parsed result
getPointsPercentage() // An percentage of the total obtained achievements
getTotalAchievements() // Total achievements obtainable from parsed result
getCurrentAchievements() // Total achievements the character has obtained for this parsed result
```

**FreeCompany**
```PHP
getID()
getLodestone() // the url of the lodestone profile
getCompany() // maelstrom, twin adder, immortal flames.
getName()
getServer()
getTag()
getFormed()
getMemberCount()
getSlogan()
getMembers() // array of members containing: id, name, server, rank, rank image, class icon, class level
```

**Linkshell**
```PHP
getID()
getLodestone() // the url of the lodestone profile
getName()
getServer()
getTotalMembers() // a count
getMembers() // array of members containing: id, avatar, name, server, rank, class(icon lv), company (icon, name, rank), freecompany (icon, id, name)
```

**Stat Types**
Type > Attribute.
```php
// Example use:
echo $Character->getStat('offense', 'critical hit rate');
```
*   core
    *   hp
    *   mp
    *   tp
*   attributes
    *   strength
    *   vitality
    *   dexterity
    *   intelligence
    *   mind
    *   piety
*   elemental
    *   fire
    *   ice
    *   wind
    *   earth
    *   lightning
    *   water
*   offense
    *   accuracy
    *   critical hit rate
    *   determination
*   defense
    *   defense
    *   parry
    *   magic defense
*   physical
    *   attack power
    *   skill speed
*   resists
    *   slashing
    *   piercing
    *   blunt
*   crafting
    *   craftsmanship
    *   control
*   spell
    *   attack magic potency
    *   healing magic potency
    *   spell speed
*   pvp
    *   morale
*   gathering
    *   gathering
    *   Perception

**Slot Types**
```php
// Example use:
echo $Character->getSlot('head');
```
*   main - main hand weapon or tool
*   shield
*   head
*   body
*   waist
*   legs
*   feet
*   necklace
*   earrings
*   bracelets
*   ring
*   ring2
*   soul crystal

**Achievements**
todo...



