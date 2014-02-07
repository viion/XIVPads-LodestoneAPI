Lodestone RSS
=============
XIVPads.com generates an json file that can be used as an RSS on your own website to show announcements made on the official lodestone. The file contains a total count and the data of each entry in "topics".

**Details**
- Parses 20 of the latest topics
- Updates every 1 hour
- Language: json

**File**
- http://xivpads.com/lodestone_rss.json

Parsing
-------
You can use file_get_getcontents() and json_decode() to parse the data in PHP.
```php
// Get array data
$Data = json_decode(file_get_contents('http://xivpads.com/lodestone_rss.json'), true);
```

The data array returns like so:
```js
Array
(
    [total] => 20
    [data] => Array
        (
            [0] => Array
                (
                    [time] => 1387450800
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/5a87a69a84f21addd969ccbb81fe4b7e5e3a743a
                    [title] => Announcing the Starlight Celebration Screenshot Contest!
                    [image] => http://img.finalfantasyxiv.com/t/5a87a69a84f21addd969ccbb81fe4b7e5e3a743a.png
                )

            [1] => Array
                (
                    [time] => 1387353600
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/2ed58470896bbe8873da608169b498e3e02dba32
                    [title] => The Starlight Celebration - Forever Winter Nights
                    [image] => http://img.finalfantasyxiv.com/t/2ed58470896bbe8873da608169b498e3e02dba32.png
                )

            [2] => Array
                (
                    [time] => 1387284300
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/cb7713d1cc385c44a3cd03af284f2e9eaec80e81
                    [title] => Regarding Housing Prices
                    [image] => http://img.finalfantasyxiv.com/t/cb7713d1cc385c44a3cd03af284f2e9eaec80e81.png
                )

            [3] => Array
                (
                    [time] => 1387039800
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/ca4d55a689dcd91be77d9546b83cf42224bd8726
                    [title] => Patch 2.1 Notes
                    [image] => http://img.finalfantasyxiv.com/t/ca4d55a689dcd91be77d9546b83cf42224bd8726.png
                )

            [4] => Array
                (
                    [time] => 1387031400
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/3323dd8c522981848453605e713191dde91edb10
                    [title] => Enter the Wolves' Den
                    [image] => http://img.finalfantasyxiv.com/t/3323dd8c522981848453605e713191dde91edb10.png
                )

            [5] => Array
                (
                    [time] => 1387031100
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/2cbef3fca243fc9099c206e3efa611a9d24b19f4
                    [title] => Treasure Hunt Preview
                    [image] => http://img.finalfantasyxiv.com/t/2cbef3fca243fc9099c206e3efa611a9d24b19f4.png
                )

            [6] => Array
                (
                    [time] => 1387030800
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/f08af4343fb2517fe5d384d567ca7e67919f7f7a
                    [title] => Beast Tribe Quest Preview
                    [image] => http://img.finalfantasyxiv.com/t/f08af4343fb2517fe5d384d567ca7e67919f7f7a.png
                )

            [7] => Array
                (
                    [time] => 1386846900
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/3cfad29b51fcad14f3665c54b843d5232ab63217
                    [title] => Side Story Quest Preview
                    [image] => http://img.finalfantasyxiv.com/t/3cfad29b51fcad14f3665c54b843d5232ab63217.png
                )

            [8] => Array
                (
                    [time] => 1386764100
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/90554fd86aa4822cb37bd67905b2c1f34f8649fc
                    [title] => FFXIV: ARR Free Company Recruitment Contest
                    [image] => http://img.finalfantasyxiv.com/t/90554fd86aa4822cb37bd67905b2c1f34f8649fc.png
                )

            [9] => Array
                (
                    [time] => 1386679500
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/963822b863a62b40a7e1a11cac4379680f38707e
                    [title] => Primal Battles Preview
                    [image] => http://img.finalfantasyxiv.com/t/963822b863a62b40a7e1a11cac4379680f38707e.png
                )

            [10] => Array
                (
                    [time] => 1386678600
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/044c097bf9e5fac0ccc0c280606c26e49cb4189a
                    [title] => Letter from the Producer LIVE Part XI -UPDATE-
                    [image] => http://img.finalfantasyxiv.com/t/044c097bf9e5fac0ccc0c280606c26e49cb4189a.png
                )

            [11] => Array
                (
                    [time] => 1386316800
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/e14494460013635a06122dd50966e1369a718744
                    [title] => Patch 2.1 -- A Realm Awoken Trailer Now Live!
                    [image] => http://img.finalfantasyxiv.com/t/e14494460013635a06122dd50966e1369a718744.png
                )

            [12] => Array
                (
                    [time] => 1386306900
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/7d6ccedbd4f7d12263a6907bde201733151020fc
                    [title] => Lightning Strikes Event Reminder
                    [image] => http://img.finalfantasyxiv.com/t/7d6ccedbd4f7d12263a6907bde201733151020fc.png
                )

            [13] => Array
                (
                    [time] => 1386158400
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/3051cbafca3e54d0dbfe5d198c501cce1d8e6832
                    [title] => Letter from the Producer, LII
                    [image] => http://img.finalfantasyxiv.com/t/3051cbafca3e54d0dbfe5d198c501cce1d8e6832.png
                )

            [14] => Array
                (
                    [time] => 1386075600
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/f80418cf0e4b412e3bf98b884f76d0561ed60f6a
                    [title] => FINAL FANTASY XIV: A REALM REBORN RECEIVES "SPECIAL AWARD" AT PLAYSTATION
                    [image] => http://img.finalfantasyxiv.com/t/f80418cf0e4b412e3bf98b884f76d0561ed60f6a.png
                )

            [15] => Array
                (
                    [time] => 1386068400
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/61eab4087cd2e5fdf9b75d80c23134e809eb66ea
                    [title] => Crystal Tower Preview
                    [image] => http://img.finalfantasyxiv.com/t/61eab4087cd2e5fdf9b75d80c23134e809eb66ea.png
                )

            [16] => Array
                (
                    [time] => 1385987400
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/0ba3278ae4e92d6f21ecac245bcee0a9c6dbf970
                    [title] => The "Letter from the Producer LIVE Part X" Video & Q&A Released!
                    [image] => http://img.finalfantasyxiv.com/t/0ba3278ae4e92d6f21ecac245bcee0a9c6dbf970.png
                )

            [17] => Array
                (
                    [time] => 1385971200
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/bc2a4975fd84666c1b9a9f84325faf57d9604f65
                    [title] => Archiving of the Old Lodestone
                    [image] => http://img.finalfantasyxiv.com/t/bc2a4975fd84666c1b9a9f84325faf57d9604f65.png
                )

            [18] => Array
                (
                    [time] => 1385724600
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/be4ccd00484db59495218a12055b445d5e5a0781
                    [title] => Letter from the Producer LIVE Part XI
                    [image] => http://img.finalfantasyxiv.com/t/be4ccd00484db59495218a12055b445d5e5a0781.png
                )

            [19] => Array
                (
                    [time] => 1385539200
                    [url] => http://eu.finalfantasyxiv.com/lodestone/topics/detail/867d2ab1d3edd4da13ff28d0dacc8eb228a2eade
                    [title] => Dungeon Preview
                    [image] => http://img.finalfantasyxiv.com/t/867d2ab1d3edd4da13ff28d0dacc8eb228a2eade.png
                )

        )

)
```
