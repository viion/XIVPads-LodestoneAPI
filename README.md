# Lodestone API (by XIVPads)
An API for parsing lodestone data, designed and maintained by XIVPads.com and XIVDB.com

## Stuff
**Requirements**
- PHP 5.4 (this wont change)
- CURL Enabled on your Server (use phpinfo() to check this)
- Moderate understanding of Object-Oriented Programming
- 500 Gil

**General Notes**
- If you have problems, please create an issue on here.
- The tab indent size is "4 spaces", if you set your IDE to this it will look nice.

**Big Thanks (v5):**
- @JohnRamboTSQ for all his pro reg-ex on the new v5 API, creating a sustainable memory usage and fixing memory leaks. All "Advanced" functions are created by this guy, as well as various additions to data such as extended item information.

**Big Thanks (v4):**
- @eedev for (Gear level, ID Lodestone and "Cannot equip gear to" data on the gear array) https://github.com/viion/XIVPads-LodestoneAPI/pull/32
- @stygiansabyss for patch 2.1 free company icon fix

**License**
- MIT License : Copyright (c) 2014 Josh Freeman
- You may: use, redistribute, modify, share, collaborate, change spaces to tabs, so long as the comment license stays intack at the top. IF YOU MAKE MODIFICATIONS please add your contribution details (name/git handle) in the readme

**Old API**
- If you are looking for the old API, have a wonder to the ``` _old ``` directory. This will be deleted when the FFXIV expansion goes live. Or maybe sooner.

**Javascript Version**
- There is a Javascript version of the API, instead of parsing Lodestone it parses XIVSync. This could be faster for you as our XIVSync server is in Japan. It also fetches all the information that XIVPads gathers such as Events and includes Achievements without additional page loads. This is still quite beta so test it to get used to it. There is a test.html file included in the repo which you can use to test/try and see how it works. I've included an example in there.
- You can host it yourself or it can also find the file at: http://xivsync.com/lodestoneapi.js (minified: http://xivsync.com/lodestoneapi.min.js)

**Example / Tests**
- View ``` test.php ``` for some good examples and tests. Below is the basics.

---

## Coding Style

Most of the coding style is straight forward PHP Standards. Accessing chained classes are Capitalized and functions which return a class will also be Capitalized, for example:

```php
$api->Search->Character($name, $world);
```

All data is accessed directly, or can be "dumped" as a single array, eg:

```php
$character = $api->Search->Character('Premium Virtue', 'Hyperion');

// print name
echo $character->name;

// array of all variables
var_dump($character->dump());
```

---

## Getting Started

### Composer

For composer:

``` composer require viion/xivpads-lodestoneapi ```

then within your code:

```php
use Viion\Lodestone\LodestoneAPI;
$api = new LodestoneAPI();
 ```

or if you prefer:

``` $api = new Viion\Lodestone\LodestoneAPI(); ```

### Without composer

If you are not using composer, then edit the ```path/to/api/``` to where you have the api files and include the ```api-autoloader.php``` file.

```php
<?php
require 'path/to/api/api-autoloader.php';
$api = new Viion\Lodestone\LodestoneAPI();
```

---

## API

### Character

Example on getting character data

```php
<?php
require 'api-autoloader.php';
$api = new Viion\Lodestone\LodestoneAPI();

// Search by: name + world
$character = $api->Search->Character('name', 'world');

// Search by: lodestone id
$character = $api->Search->Character(1234567);

// Get a dump of all data in an array
var_dump( $character->dump() );

// Basic data
echo $character->name;
echo $character->world;
var_dump( $character->classjobs );
```

Accessible data

```php
id
name
world
title
avatar
avatarHash
portrait
bio
race
clan
gender
nameday

guardian
guardianIcon
city
cityIcon
grandCompany
grandCompanyRank
grandCompanyIcon
freeCompany
freeCompanyId
freeCompanyIcon

classjobs (array)
gear (array)
gearBonus (array)
gearStats (array)
attributes (array)

activeClass
activeJob
activeLevel

minions (array)
mounts (array)

hash
events
all50
```

# work in progres
# ...
