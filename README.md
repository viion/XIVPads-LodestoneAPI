XIVPads-LodestoneAPI
====================
An API for parsing lodestone data, designed and maintained by XIVPads.com and XIVDB.com

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
- Free Company

**Todo**
- Clean up some code
- Modularize achievements to characters
- Linkshell Parse
- Friend Parse
- Blog Parse

Getting Started
--------
The API is heavily object-oriented. It will parse the Lodestone for information and attach this to a 
entity type object, for example searching a character and parsing the profile will create a character
object pre-filled with the profile information where you can call traditional "get" functions.

**Quick Parse**
```php
// Initialize a LodestoneAPI Obkect
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
categories, I plan to make a standalone function call that can be looped locally.

```php
// Set an ID
$ID = 730968;

// Parse achievements
$API->parseAchievements($ID)

// Show achievements
Show($API->getAchievements());
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
Show($FreeCompany->getMembers();
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


API Methods
--------

**LodestoneAPI**
```php
// Quick Get functions
get(array) (array takes: "name" => "xxx", "server" => "xxx", "id" => "123", ID will take priority)
getFC(array, config[optional]) (array takes: "name" => "xxx", "server" => "xxx", "id" => "123, ID will take priority)

// Search functions
searchCharacter(name, server, exact[true|false])
searchFreeCompany(name, server, exact[true|false])

// Get Functions
getSearch()
getCharacters()
getCharacterByID(id)
getFreeCompanies
getFreeCompanyByID(id)
getAchievements() * Going to be depreciated and move to character object

// Parse
parseProfile(id)
parseBiography(id)
parseAchievements()
parseAchievementsByCategory(cID)
parseFreeCompany(id, options[optional])

// Error check
errorPage(id)
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
getMinions() // Returns array containing 'Name' and 'Icon'
getClassJob(class) // Get level/exp info of a class
isValid() // Wheather character data is valid or not (WIP)
getErrors() // List of errors found during validation
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



