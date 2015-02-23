XIVPads-LodestoneAPI
====================
An API for parsing lodestone data, designed and maintained by XIVPads.com and XIVDB.com

**Requirements**
- PHP 5.4 (this wont change)
- CURL Enabled on your Server (use phpinfo() to check this)
- Moderate understanding of Object-Oriented Programming
- 500 Gil

**General Notes**
- If you have problems, please create an issue on here.
- The tab indent size is "4 spaces", if you set your IDE to this it will look nice.

**Big Thanks:**
- @JohnRamboTSQ for adding: Big Portrait and Gender detection!, + Blog and World Status!!! Helping migrate to phpQuery
- @FlyerUA for bug detection and additional functionality: probableJobs()
- @eedev for (Gear level, ID Lodestone and "Cannot equip gear to" data on the gear array) https://github.com/viion/XIVPads-LodestoneAPI/pull/32
- @stygiansabyss for patch 2.1 free company icon fix

**License**
- MIT License : Copyright (c) 2014 Josh Freeman
- You may: use, redistribute, modify, share, collaborate, change spaces to tabs, so long as the comment license stays intack at the top. IF YOU MAKE MODIFICATIONS please add your contribution details (name/git handle) in the readme

**Old API**
If you are looking for the old API, have a wonder to the ``` _old ``` directory. This will be deleted when the FFXIV expansion goes live.

**Example / Tests**
View ``` test.php ``` for some good examples and tests. Below is the basics.

**Coding style**
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

*API*

todo ...