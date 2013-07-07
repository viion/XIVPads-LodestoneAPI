XIVPads-LodestoneAPI
====================
An API for parsing lodestone data, designed and maintained by XIVPads.com and XIVDB.com

**Requirements**
- PHP 5.4 (Function array dereferencing http://php.net/manual/en/migration54.new-features.php)
- CURL Enabled on your Server (use phpinfo() to check this)
- Moderate understanding of Object-Oriented Programming
- 500 Gil

**Implemented**
- Character Search
- Profile Parse
- Achievement Parse

**Todo**
- Clean up some code
- Individual Achievement Parse using an Category ID
- Linkshell Parse
- Free Company Parse
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
$Character = $API->get("Premium Virtue", "Gungnir");

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

API Methods
--------

**LodestoneAPI**
```php
searchCharacter(name, server, exact[true|false])
get(name, server)
getSearch()
getAchievements()
getCharacters()
getCharacterByID(id)
parseProfile(id)
parseBiography(id)
parseAchievements(id)
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
getFreeCompany()
getCity()
getBiography()
getStat(type, attribute) // (List below)
getGear() // Returns list containing 'name', 'icon', 'slot'
getEquipped(type) // List by 'numbers', or list by 'slots'
getSlot() // (List below)
getActiveClass()
getActiveJob()
getActiveLevel()
getMinions() // Returns list containing 'Name' and 'Icon'
getClassJob(class) // Get level/exp info of a class
isValid() // Wheather character data is valid or not (WIP)
getErrors() // List of errors found during validation
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



