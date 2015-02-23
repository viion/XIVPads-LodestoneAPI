<?php
    /*
        XIVPads.com (v4) - Lodestone Query API
        --------------------------------------------------
        Author:     Josh Freeman (Premium Virtue)
        Support:    http://xivpads.com/?Portal
        Version:    5
        PHP:        5.4

        Always ensure you download from the github
        https://github.com/viion/XIVPads-LodestoneAPI
        --------------------------------------------------
        If you have an auto loader, either change the namespace
        or put the API into /api/lodestone/api.php

        Legacy new LodestoneAPI(); will still work.
        --------------------------------------------------
        Note on foreign character results, these in raw format
        will render very weird, this happens when debugging
        and it is usually because no charset, you can add a
        meta tag to your document to get true output.

            <meta charset="UTF-8">

        View the test.php script as an example.
    */

    // Debug stuff
    //error_reporting(-1);

    // Namespace
    namespace Viion\Lodestone;
	require_once(dirname(__FILE__).'/phpQuery.php');

    /*  trait 'Funky'
     *  Cool functions that all classes will get access to
     */
    trait Funky
    {
        /**
         * Return Unicode for a character
         * @param string $u
         * @return int
         */
        function uniord($u) {
            $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
            $k1 = ord(substr($k, 0, 1));
            $k2 = ord(substr($k, 1, 1));
            return $k2 * 256 + $k1;
        }
        /*  - sƒow
         *  Shows the contents of an object.
         */
        function show($data = null)
        {
            // If there is no data, replace it with this
            if (!$data) { $data = $this; }

            // Print it
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }

        /*  - sksort
         *  Sorts by a key. Can handle multi-dimentional arrays.
         *  It is used globally, so it modifies the pointered array, thus use it like so:
         *
         *      $array = ['some' => 'array'];
         *      $this->sksort($array, 'some');
         */
        function sksort(&$array, $subkey, $sort_ascending = false)
        {
            if (count($array))
            {
                $temp_array[key($array)] = array_shift($array);
            }

            foreach($array as $key => $val)
            {
                $offset = 0;
                $found = false;

                foreach($temp_array as $tmp_key => $tmp_val)
                {
                    if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                    {
                        $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                                    array($key => $val),
                                                    array_slice($temp_array,$offset)
                                                  );
                        $found = true;
                    }
                    $offset++;
                }

                if(!$found)
                {
                    $temp_array = array_merge($temp_array, array($key => $val));
                }
            }

            if ($sort_ascending)
            {
                $array = array_reverse($temp_array);
            }
            else
            {
                $array = $temp_array;
            }
        }

        /*  - log
         *  Sends a message to the global log variable if it exists
         */
        function log($line, $message, $Array = null)
        {
            if (array_key_exists('LodestoneAPILogger', $GLOBALS))
            {
                global $LodestoneAPILogger;

                // If we have an array, append per param
                if ($Array)
                {
                    $message = $message .' # Params: ';
                    foreach($Array as $i => $param)
                    {
                        $message = $message . '[('. $i .') '. $param .']';
                    }
                }

                // Append line number
                $message = $line .' > '. $message;

                // Log
                $LodestoneAPILogger->log($message);
            }
        }
    }

    /*  trait 'Funky'
     *  Various configuration data to be used by other classes
     */
    trait Config
    {
        // url addresses to various lodestone content. (DO NOT CHANGE, it will break some functionality of the API)
        private $URL =
        [
            # base url
            'base' => 'http://eu.finalfantasyxiv.com',

            # Search related urls
            'search' =>
            [
                'query'         => '?q=%name%&worldname=%server%',
            ],

            # Character related urls
            'character' =>
            [
                'profile'       => 'http://eu.finalfantasyxiv.com/lodestone/character/',
                'achiSummary'   => '/achievement/',
                'achievement'   => '/achievement/kind/',
                'blog'   => '/blog/',
            ],

            # Free company related urls
            'freecompany' =>
            [
                'profile'       => 'http://eu.finalfantasyxiv.com/lodestone/freecompany/',
                'member'        => '/member/',
                'memberpage'    => '?page=%page%',
            ],

            # Linkshell related urls
            'linkshell' =>
            [
                'profile'       => 'http://eu.finalfantasyxiv.com/lodestone/linkshell/',
                'activity'      => '/activity/',
            ],

            # Lodestone
            'lodestone' =>
            [
                'news'          => 'http://eu.finalfantasyxiv.com/lodestone/news/',
                'topic'         => 'http://eu.finalfantasyxiv.com/lodestone/topics/',
                'notices'       => 'http://eu.finalfantasyxiv.com/lodestone/news/category/1',
                'maintenance'   => 'http://eu.finalfantasyxiv.com/lodestone/news/category/2',
                'updates'       => 'http://eu.finalfantasyxiv.com/lodestone/news/category/3',
                'status'        => 'http://eu.finalfantasyxiv.com/lodestone/news/category/4',
				'worldstatus'	=> 'http://eu.finalfantasyxiv.com/lodestone/worldstatus/',

                // Multi language is currently not supposed as the "Dev Tracker" links change
                // based on time. I could make it parse this page, get the correct link and
                // then go parse the dev tracker links, but that is multiple curl and I dont
                // want to do that just yet. Maybe in the future!
                'forums'        =>'http://forum.square-enix.com/ffxiv/forum.php',
            ],

            ###

            # Social IDs
            'social' =>
            [
                'youtube'       => 'FINALFANTASYXIV',
            ],

            # Youtube API
            'youtube' =>
            [
                'channels'      => 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername={channel}&key={key}',
                'playlists'     => 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults={max}&playlistId={playlistID}&key={key}',
            ],

            # Because twitter is a useless piece of shit when it comes to API
            # and practically prevents public access even through app key (like youtube)
            # I will have to do the old fashion source code parse... Fun!
            'twitter' =>
            [
                'en' => 'https://twitter.com/ff_xiv_en'
            ],
        ];

        // Gamedataz
        public $AchievementCategories = [1, 2, 4, 5, 6, 8, 11, 12, 13];

        // Gear sets
        public $GearSlots =
        [
            "main","tool","shield","soul crystal",
            "head","body","hands","waist","legs","feet",
            "necklace","earrings","bracelets","ring","ring2"
        ];

        public $ClassList = [];
        public $ClassDisicpline = [];

        //---------------------------------------------------------------------
        // Keys!
        //---------------------------------------------------------------------

        // Google API key (used to parse Youtube API)
        // Change this to your own app ID if you wish.
        private $GoogleAPIKey = 'AIzaSyDWPpnQYGaiZN-AuQBNyDDSCJdy9fQcHnQ';
        public function getGoogleAPIKey() { return $this->GoogleAPIKey; }

        function __construct()
        {
            // Set classes
            $this->ClassList = array(
                "Gladiator", "Pugilist", "Marauder", "Lancer", "Archer", "Rogue", "Conjurer", "Thaumaturge", "Arcanist", "Carpenter", "Blacksmith",
                "Armorer", "Goldsmith", "Leatherworker", "Weaver", "Alchemist", "Culinarian", "Miner", "Botanist", "Fisher"
            );

            // Set class by disicpline
            $this->ClassDisicpline = array(
                "dow" => array_slice($this->ClassList, 0, 6),
                "dom" => array_slice($this->ClassList, 6, 3),
                "doh" => array_slice($this->ClassList, 9, 8),
                "dol" => array_slice($this->ClassList, 17, 3),
            );
        }
    }

    /*  LodestoneAPI
     *  ------------
     */
    class API extends Parser
    {
        use Funky;
        use Config;

        // defaults
        private $defaults =
        [
            'automaticallyParseFreeCompanyMembers' => false,
            'pagesPerFreeCompanyMemberList' => 20,
        ];

        // List of characters parsed
        public $Characters = [];
        public $Achievements = [];
        public $Search = [];
		/**
		 * Blog
		 * @var Blog 
		 */
        public $Blog = null;

        // List of free company data parsed
        public $FreeCompanyList = [];
        public $FreeCompanyMembersList = [];

        // List of linkshell data parsed
        public $Linkshells = [];

        // Initialize
        public function __construct() {}

        #-------------------------------------------#
        # SHORT GETS                                #
        #-------------------------------------------#

        /**
		 *   - get
         *  Gets a character, the array can be either "name, server" OR "id". If you
         *  pass an name and server, the API will have to search, it will then select the
         *  first result found. If you pass an ID, the search is skipped and is twice as
         *  fast and more reliable due to exact ID being known.
         *
         *  The same principle applies to getFC and getLS
         *
         *  @return Character
         **/
        public function get($Array, $Options = null)
        {
            $this->log(__LINE__, 'function: get() - start');

            // Clean
            $Name   = isset($Array['name'])     ? trim(ucwords($Array['name'])) : NULL;
            $Server = isset($Array['server'])   ? trim(ucwords($Array['server'])) : NULL;
            $ID     = isset($Array['id'])       ? trim($Array['id']) : NULL;

            // If no ID passed, find it.
            if (!$ID)
            {
                // Search by Name + Server, exact
                $this->searchCharacter($Name, $Server, true);

                // Get by specific ID
                $ID = $this->getSearch()['results'][0]['id'];
            }

            // If an ID
            if ($ID)
            {
                // Parse profile
                $this->parseProfile($ID);

                // Return character
                $this->log(__LINE__, 'function: get() - return');
                return $this->getCharacterByID($ID);
            }
            else
            {
                return false;
            }
        }

        /**  - getFC
         *  Read "get" for characters, same rules apply to this.
         *  @return FreeCompany
         **/
        public function getFC($Array, $Options = null)
        {
            // Clean
            $Name   = isset($Array['name'])     ? trim(ucwords($Array['name'])) : NULL;
            $Server = isset($Array['server'])   ? trim(ucwords($Array['server'])) : NULL;
            $ID     = isset($Array['id'])       ? trim($Array['id']) : NULL;

            // If no ID passed, find it.
            if (!$ID)
            {
                // Search by Name + Server, exact
                $this->searchFreeCompany($Name, $Server, true);

                // Get by specific ID
                $ID = $this->getSearch()['results'][0]['id'];
            }

            // If an ID
            if ($ID)
            {
                // Parse profile
                $this->parseFreeCompany($ID, $Options);

                // Return character
                return $this->getFreeCompanyByID($ID);
            }
            else
            {
                return false;
            }
        }

        /*  - getLS
         * Read "get" for characters, same rules apply to this.
         * returns: Linkshell object
         */
        public function getLS($Array, $Options = null)
        {
            // Clean
            $Name   = isset($Array['name'])     ? trim(ucwords($Array['name'])) : NULL;
            $Server = isset($Array['server'])   ? trim(ucwords($Array['server'])) : NULL;
            $ID     = isset($Array['id'])       ? trim($Array['id']) : NULL;

            // If no ID passed, find it.
            if (!$ID)
            {
                // Search by Name + Server, exact
                $this->searchLinkshell($Name, $Server, true);

                // Get by specific ID
                $ID = $this->getSearch()['results'][0]['id'];
            }

            // If an ID
            if ($ID)
            {
                // Parse profile
                $this->parseLinkshell($ID, $Options);

                // Return character
                return $this->getLinkshellByID($ID);
            }
            else
            {
                return false;
            }
        }

        // Get lodestone object
        public function Lodestone() { return new Lodestone(); }

        // Get social object
        public function Social() { return new Social(); }

        #-------------------------------------------#
        # SEARCH                                    #
        #-------------------------------------------#

        // Search a character by its name and server.
        public function searchCharacter($Name, $Server, $GetExact = true)
        {
            $this->log(__LINE__, 'function: searchCharacter()');

            if (!$Name)
            {
                echo "error: No Name Set.";
            }
            else if (!$Server)
            {
                echo "error: No Server Set.";
            }
            else
            {
                // Exact name for later
                $ExactName = $Name;
                $this->log(__LINE__, 'function: searchCharacter() - searching ...');

                // Get the source
                $this->getSource($this->URL['character']['profile'] . str_ireplace(array('%name%', '%server%'), array(str_ireplace(" ", "+", $Name), $Server), $this->URL['search']['query']));

                // Get all found characters
                $Found = $this->findAll('thumb_cont_black_50', 10, NULL, false);
                $this->log(__LINE__, 'function: searchCharacter() - got results');

                // Loop through results
                if ($Found)
                {
                    foreach($Found as $F)
                    {
                        $Avatar     = explode('&quot;', $F[1])[3];
                        $Data       = explode('&quot;', $F[6]);
                        $ID         = trim(explode('/', $Data[3])[3]);
                        $NameServer = explode("(", trim(str_ireplace(">", NULL, strip_tags(html_entity_decode($Data[4])))));
                        $Name       = htmlspecialchars_decode(trim($NameServer[0]), ENT_QUOTES);
                        $Server     = trim(str_ireplace(")", NULL, $NameServer[1]));
                        $Language   = $F[4];

                        // Append search results
                        $this->Search['results'][] = array(
                            "avatar"    => $Avatar,
                            "name"      => $Name,
                            "server"    => $Server,
                            "id"        => $ID,
                        );
                    }

                    // If to get exact
                    if ($GetExact)
                    {
                        $Exact = false;
                        foreach($this->Search['results'] as $Character)
                        {
                            //show($Character['name'] .' < > '. $ExactName);
                            //show(md5($Character['name']) .' < > '. md5($ExactName));
                            //show(strlen($Character['name']) .' < > '. strlen($ExactName));
                            $n1 = trim(strtolower($Character['name']));
                            $n2 = trim(strtolower($ExactName));
                            if ($n1 == $n2 && strlen($n1) == strlen($n2))
                            {
                                $Exact = true;
                                $this->Search['results'] = NULL;
                                $this->Search['results'][] = $Character;
                                $this->Search['isExact'] = true;
                                break;
                            }
                        }

                        // If no exist false, null array
                        if (!$Exact)
                        {
                            $this->Search = NULL;
                        }
                    }

                    // Number of results
                    $this->Search['total'] = count($this->Search['results']);
                }
                else
                {
                    $this->Search['total'] = 0;
                    $this->Search['results'] = NULL;
                }
            }
        }

        // Search a free company by name and server
        public function searchFreeCompany($Name, $Server, $GetExact = true)
        {
            if (!$Name)
            {
                echo "error: No Name Set.";
            }
            else if (!$Server)
            {
                echo "error: No Server Set.";
            }
            else
            {
                // Exact name for later
                $ExactName = $Name;

                // Get the source
                $this->getSource($this->URL['freecompany']['profile'] . str_ireplace(array('%name%', '%server%'), array(str_ireplace(" ", "+", $Name), $Server), $this->URL['search']['query']));

                // Get all found data
                $Found = $this->findAll('ic_freecompany_box', null, '/tr', false);

                // if found
                if ($Found)
                {
                    foreach($Found as $F)
                    {
                        $Temp = [];
                        foreach($F as $i => $line)
                        {
                            if (stripos($line, 'ic_crest_64') !== false)
                            {
                                $offset = $i + 2;
                                $Temp['emblum'][] = $this->getAttribute('src', $F[$offset]);
                                $Temp['emblum'][] = $this->getAttribute('src', $F[$offset + 1]);
                                $Temp['emblum'][] = $this->getAttribute('src', $F[$offset + 2]);
                            }

                            if (stripos($line, 'groundcompany_name') !== false)
                            {
                                $Temp['grandcompany'] = $this->strip_html($line);
                            }

                            if (stripos($line, 'player_name_gold') !== false)
                            {
                                $offset = $i + 1;
                                $data = explode('(', $this->strip_html($F[$offset]));
                                $Temp['name'] = trim($data[0]);
                                $Temp['server'] = trim(str_ireplace(')', null, $data[1]));

                                $Temp['id'] = explode('/', $F[$offset])[3];
                                $Temp['url'] = $this->URL['freecompany']['profile'] . $Temp['id'];
                            }

                            if (stripos($line, 'ldst_strftime') !== false)
                            {
                                $Temp['formed'] = explode('(', $line)[2];
                                $Temp['formed'] = explode(',', $Temp['formed'])[0];
                            }
                        }

                        $this->Search['results'][] = $Temp;
                    }

                    // If to get exact
                    if ($GetExact)
                    {
                        $Exact = false;
                        foreach($this->Search['results'] as $FreeCompany)
                        {
                            $n1 = trim(strtolower($FreeCompany['name']));
                            $n2 = trim(strtolower($ExactName));
                            if ($n1 == $n2 && strlen($n1) == strlen($n2))
                            {
                                $Exact = true;
                                $this->Search['results'] = NULL;
                                $this->Search['results'][] = $FreeCompany;
                                $this->Search['isExact'] = true;
                                break;
                            }
                        }

                        // If no exist false, null array
                        if (!$Exact)
                        {
                            $this->Search = NULL;
                        }
                    }

                    // Number of results
                    $this->Search['total'] = count($this->Search['results']);
                }
                else
                {
                    $this->Search['total'] = 0;
                    $this->Search['results'] = NULL;
                }
            }
        }

        // Search a linkshell by name and server
        public function searchLinkshell($Name, $Server, $GetExact = true)
        {
            if (!$Name)
            {
                echo "error: No Name Set.";
            }
            else if (!$Server)
            {
                echo "error: No Server Set.";
            }
            else
            {
                // Exact name for later
                $ExactName = $Name;

                // Get the source
                $this->getSource($this->URL['linkshell']['profile'] . str_ireplace(array('%name%', '%server%'), array(str_ireplace(" ", "+", $Name), $Server), $this->URL['search']['query']));

                // Get all found data
                $Found = $this->findAll('player_name_gold linkshell_name', 5, NULL, false);

                // if found
                if ($Found)
                {
                    foreach($Found as $F)
                    {
                        $ID         = trim(explode("/", $F[0])[3]);
                        $Name       = trim(str_ireplace(['&quot;', '&lt;', '&gt;'], null, explode("/", $F[0])[4]));
                        $Server     = trim(strip_tags(html_entity_decode(str_ireplace(")", null, explode("(", $F[0])[1]))));
                        $Members    = trim(explode(":", strip_tags(html_entity_decode($F[3])))[1]);

                        $this->Search['results'][] =
                        [
                            "id"        => $ID,
                            "name"      => $Name,
                            "server"    => $Server,
                            "members"   => $Members,
                        ];
                    }

                    // If to get exact
                    if ($GetExact)
                    {
                        $Exact = false;
                        foreach($this->Search['results'] as $Linkshell)
                        {
                            $n1 = trim(strtolower($Linkshell['name']));
                            $n2 = trim(strtolower($ExactName));
                            if ($n1 == $n2 && strlen($n1) == strlen($n2))
                            {
                                $Exact = true;
                                $this->Search['results'] = NULL;
                                $this->Search['results'][] = $Linkshell;
                                $this->Search['isExact'] = true;
                                break;
                            }
                        }

                        // If no exist false, null array
                        if (!$Exact)
                        {
                            $this->Search = NULL;
                        }
                    }

                    // Number of results
                    $this->Search['total'] = count($this->Search['results']);
                }
                else
                {
                    $this->Search['total'] = 0;
                    $this->Search['results'] = NULL;
                }
            }
        }

        // Get search results
        public function getSearch() { return $this->Search; }

        // Checks if an error page exists
        public function errorPage($ID)
        {
            // Check error message
            $PageNotFound = $this->find('base_visual_error');

            // if error message is found.
            if ($PageNotFound) { return true; }

            return false;
        }

        #-------------------------------------------#
        # PROFILE                                   #
        #-------------------------------------------#

        // Parse a profile based on ID (skips searching)
        public function parseProfile($ID)
        {

            $this->log(__LINE__, 'function: parseProfile() - parsing profile: '. $ID);

            if (!$ID)
            {
                echo "error: No ID Set.";
            }

            // Get the source
            $this->log(__LINE__, 'function: parseProfile() - get source');
            $this->getSource($this->URL['character']['profile'] . $ID);
            $this->log(__LINE__, 'function: parseProfile() - obtained source');

            if ($this->errorPage($ID))
            {
                echo "error: Character page does not exist.";
            }
            else
            {
                $this->log(__LINE__, 'function: parseProfile() - starting parse');

                // Create a new character object
                $Character = new Character();
                $this->log(__LINE__, 'function: parseProfile() - new character object');

                // Set Character Data
                $Character->setID(trim($ID), $this->URL['character']['profile'] . $ID);
                $Character->setNameServer($this->findRange('player_name_thumb', 15));

                $this->log(__LINE__, 'function: parseProfile() - set id, name and server');

                // Only process if character name set
                if (strlen($Character->getName()) > 3)
                {
                    $this->log(__LINE__, 'function: parseProfile() - parsing chunk 1');

                    $Character->setTitle($this->findRange('chara_title', 2, NULL, false));
                    $Character->setAvatar($this->findRange('player_name_thumb', 10, NULL, false));
                    $Character->setPortrait($this->findRange('bg_chara_264', 2, NULL, false));
                    $Character->setRaceClan($this->find('chara_profile_title'));
                    //$Character->setLegacy($this->find('bt_legacy_history'));
                    $Character->setNamedayCityCompanyFC($this->findRange('chara_profile_left', null, "chara_class_box", false));

                    $Character->setCity($this->findRange('City-state', 5));
                    $Character->setBiography($this->findRange('txt_selfintroduction', 5));
                    $Character->setStats($this->findAll('param_left_area_inner', 12, null, false));
                    $Character->setHPMPTP($this->findRange('param_power_area', 10));
                    $Character->setActiveClassLevel($this->findAll('class_info', 5, null, false));

                    $this->log(__LINE__, 'function: parseProfile() - parsing chunk 2');

                    // Set Gear (Also sets Active Class and Job), then set item level from the gear
                    $Character->setGear($this->findAll('-- ITEM Detail --', NULL, '-- //ITEM Detail --', false));
                    $Character->setItemLevel($this->GearSlots);

                    #$this->segment('area_header_w358_inner');
                    $this->log(__LINE__, 'function: parseProfile() - parsing chunk 3');

                    // Set Minions
                    $Minions = $this->findRange('-- Minion --', NULL, '//Minion', false);
                    $Character->setMinions($Minions);

                    // Set Mounts
                    $this->log(__LINE__, 'function: parseProfile() - parsing chunk 4');
                    $Mounts = $this->findRange('-- Mount --', NULL, '//Mount', false);
                    $Character->setMounts($Mounts);

                    #$this->segment('class_fighter');

                    // Set ClassJob
                    $this->log(__LINE__, 'function: parseProfile() - parsing chunk 5');
                    $Character->setClassJob($this->findRange('class_fighter', NULL, '//Class Contents', false));

                    // Validate data
                    $Character->validate();
                    $this->log(__LINE__, 'function: parseProfile() - complete profile parse for: '. $ID);

                    // Append character to array
                    $this->Characters[$ID] = $Character;
                }
                else
                {
                    $this->Characters[$ID] = NULL;
                }
            }
        }

        // Parse just biography, based on ID
        public function parseBiography($ID)
        {
            // Get the source
            $this->getSource($this->URL['character']['profile'] . $ID);

            // Create a new character object
            $Character = new Character();

            // Get biography
            $Character->setBiography($this->findRange('txt_selfintroduction', 5));

            // Return biography
            return $Character->getBiography();
        }

        // Get a list of parsed characters
        public function getCharacters() { return $this->Characters; }

        /**
		 * Gett Charcater by id
		 * @param int $ID
		 * @return Character Get a character by id
		 */
        public function getCharacterByID($ID) { return isset($this->Characters[$ID]) ? $this->Characters[$ID] : NULL; }

        #-------------------------------------------#
        # ACHIEVEMENTS                              #
        #-------------------------------------------#

        // Parse a achievements based on ID
        public function parseAchievements($ID = null)
        {
            if (!$ID)
            {
                $ID = $this->getID();
            }

            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else
            {
                // Main achievement object
                $MA = new Achievements();

                // Loop through categories
                foreach($this->AchievementCategories as $cID)
                {
                    // Parse Achievements
                    $this->parseAchievementsByCategory($cID, $ID);

                    // Get Achievement Object
                    $A = $this->Achievements[$cID];

                    // Add onto main achievements object
                    $MA->setTotalPoints($MA->getTotalPoints() + $A->getTotalPoints());
                    $MA->setCurrentPoints($MA->getCurrentPoints() + $A->getCurrentPoints());
                    $MA->setTotalAchievements($MA->getTotalAchievements() + $A->getTotalAchievements());
                    $MA->setCurrentAchievements($MA->getCurrentAchievements() + $A->getCurrentAchievements());
                    $MA->genPointsPercentage();
                    $MA->addAchievements($A->get());
                    $MA->addCategory($cID);
                }

                // Format Achievements
                $this->Achievements = $MA;
            }
        }

        // Parse achievement by category
        public function parseAchievementsSummary($ID = null)
        {
            if (!$ID)
            {
                $ID = $this->getID();
            }

            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else
            {
                // Get the source
                $this->getSource($this->URL['character']['profile'] . $ID . $this->URL['character']['achiSummary']);

                // Create a new character object
                $Achievements = new Achievements();

                // Get Achievements
                $Public = $Achievements->checkIfPublic($this->findAll('area_inner_tc', 20));

                if ($Public)
                {
                    $Achievements->setSummary($this->findAll('achievement_area_footer', NULL, '/li', false));

                    // Append character to array
                    return $Achievements;
                }
                else
                {
                    return false;
                }
            }
        }

        // Parse achievement by category
        public function parseAchievementsByCategory($cID, $ID = null)
        {
            if (!$ID)
            {
                $ID = $this->getID();
            }

            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else if (!$cID)
            {
                echo "No catagory id set.";
            }
            else
            {
                // Get the source
                $this->getSource($this->URL['character']['profile'] . $ID . $this->URL['character']['achievement'] . $cID .'/');

                // Create a new character object
                $Achievements = new Achievements();

                // Get Achievements
                $Public = $Achievements->checkIfPublic($this->findAll('area_inner_tc', 20));

                if ($Public)
                {
                    // Get Achievements
                    $Achievements->addCategory($cID);
                    $Achievements->set($this->findAll('achievement_area_body', NULL, 'bt_more', false));

                    // Append character to array
                    return $Achievements;
                }
                else
                {
                    return false;
                }

                // Append character to array
                $this->Achievements[$cID] = $Achievements;

                return $Achievements;
            }
        }

        // Get a list of parsed characters
        public function getAchievements() { return $this->Achievements; }

        // Get the achievement categories
        public function getAchievementCategories() { return $this->AchievementCategories; }

        

        #-------------------------------------------#
        # BLOG                                      #
        #-------------------------------------------#

        // Parse achievement by CharacterID
        public function parseBlog($ID = null)
        {
            if (!$ID)
            {
                $ID = $this->getID();
            }

            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else
            {
                $Blog = new Blog($ID);
				$Blog->setEntries();
				$this->Blog = $Blog;
            }
        }

        /**
		 * Get a list of blogEntries
		 * @return Blog
		 **/
        public function getBlog() { return $this->Blog; }
        /**
		 * Get a blogEntryById
		 * @return array
		 **/
        public function getBlogByID($bID) { return $this->Blog->getEntryByID($bID); }

        #-------------------------------------------#
        # FREE COMPANY                              #
        #-------------------------------------------#

        // Parse free company profile
        public function parseFreeCompany($ID, $Options = null)
        {
            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else
            {
                // Options
                $this->defaults['automaticallyParseFreeCompanyMembers'] = (isset($Options['members'])) ? $Options['members'] : $this->defaults['automaticallyParseFreeCompanyMembers'];

                // Get source
                $this->getSource($this->URL['freecompany']['profile'] . $ID);

                // Create a new character object
                $FreeCompany = new FreeCompany();

                // Set Character Data
                $FreeCompany->setID(trim($ID), $this->URL['freecompany']['profile'] . $ID);
                $FreeCompany->setBasicData($this->findRange('crest_id centering_h', 10));
                $FreeCompany->setEmblum($this->findRange('ic_crest_64', 10, null, false));
                $FreeCompany->setFullDetails($this->findRange('-- Company Profile --', null, '-- //Company Profile --', false));

                // If to parse free company members
                if ($this->defaults['automaticallyParseFreeCompanyMembers'])
                {
                    // Temp array
                    $MembersList = [];

                    // Get number of pages
                    $TotalPages = ceil(round(intval($FreeCompany->getMemberCount()) / intval(trim($this->defaults['pagesPerFreeCompanyMemberList'])), 10));

                    // Get all members
                    for($Page = 1; $Page <= $TotalPages; $Page++)
                    {
                        // Parse Members page
                        $this->getSource($FreeCompany->getLodestone() . $this->URL['freecompany']['member'] . str_ireplace('%page%', $Page, $this->URL['freecompany']['memberpage']));

                        // Set Members
                        $MemberArray = $FreeCompany->parseMembers($this->findAll('thumb_cont_black_50', null, '/message_ic_box', false));

                        // Merge existing member list with new member array
                        $MembersList = array_merge($MembersList, $MemberArray);
                    }

                    // End point for member list
                    $FreeCompany->setMembers($MembersList);
                }

                // Save free company
                $this->FreeCompanies[$ID] = $FreeCompany;
            }
        }

        // Get a list of parsed free companies.
        public function getFreeCompanies() { return $this->FreeCompanies; }

        // Get a free company by id
        public function getFreeCompanyByID($ID) { return isset($this->FreeCompanies[$ID]) ? $this->FreeCompanies[$ID] : NULL; }

        #-------------------------------------------#
        # LINKSHELL                                 #
        #-------------------------------------------#

        // Parse free company profile
        public function parseLinkshell($ID, $Options = null)
        {
            if (!$ID)
            {
                echo "error: No ID Set.";
            }
            else
            {
                // Get source
                $this->getSource($this->URL['linkshell']['profile'] . $ID);

                // Create a new character object
                $Linkshell = new Linkshell();

                // Set Character Data
                $Linkshell->setID(trim($ID), $this->URL['linkshell']['profile'] . $ID);
                $Linkshell->setNameServer($this->findRange('player_name_brown', 15));
                $Linkshell->setMemberCount($this->findRange('ic_silver', 5));
                $Linkshell->setMembers($this->findAll('thumb_cont_black_50', null, "/tr", false));


                // Save free company
                $this->Linkshells[$ID] = $Linkshell;
            }
        }

        // Get a list of parsed linkshells.
        public function getLinkshells() { return $this->Linkshells; }

        // Get a linkshell by id
        public function getLinkshellByID($ID) { return isset($this->Linkshells[$ID]) ? $this->Linkshells[$ID] : NULL; }

        #-------------------------------------------#
        # TESTS                                     #
        #-------------------------------------------#

        // if raw, just does a simple page parse, otherwise does a full api parse
        public function testConnection($raw = false)
        {
            $id = 730968;
            $data =
            [
                'id' => $id,
                'url' => $this->URL['character']['profile'] . $id,
                'start' =>
                [
                    'time' => microtime(true),
                    'memory' => memory_get_usage(true),
                ]
            ];

            // Get a page
            if ($raw)
            {
                $this->getSource($this->URL['character']['profile'] . $id);
            }
            else
            {
                $this->parseProfile($id);
            }

            $data['finish'] =
            [
                'time' => microtime(true),
                'memory' => memory_get_usage(true),
            ];

            $data['peak_memory'] = $this->testconvertMemoryUsage(memory_get_peak_usage(true));
            $data['duration'] = floatval($data['finish']['time']) - floatval($data['start']['time']) .' ms';
            $data['sequential'] = 60 / $data['duration'];

            $output =
            [
                'started on: '. $data['start']['time'],
                'finished on: '. $data['finish']['time'],
                'duration: '. $data['duration'],
                'sequential 60s: '. $data['sequential'],
                'memory started: '. $this->testconvertMemoryUsage($data['start']['memory']),
                'memory finish: '. $this->testconvertMemoryUsage($data['finish']['memory']),
                'memory usage: '. $this->testconvertMemoryUsage($data['finish']['memory'] - $data['start']['memory']),
                'memory peak: '. $data['peak_memory'],
            ];

            $this->show($output);
        }

        public function testConnectionLoop($number)
        {
            if (!$number || !is_numeric($number)) return;

            $data =
            [
                'id' => $id,
                'url' => $this->URL['character']['profile'] . $id,
                'start' =>
                [
                    'time' => microtime(true),
                    'memory' => memory_get_usage(true),
                ]
            ];

            for ($i=0; $i < $number; $i++) {
                $this->testConnection();
                flush();
            }

                      $data['finish'] =
            [
                'time' => microtime(true),
                'memory' => memory_get_usage(true),
            ];

            $data['peak_memory'] = $this->testconvertMemoryUsage(memory_get_peak_usage(true));
            $data['duration'] = floatval($data['finish']['time']) - floatval($data['start']['time']) .' ms';
            $data['sequential'] = 60 / $data['duration'];

            $this->show('-------------------------------');

            $output =
            [
                'loops: '. $number,
                'started on: '. $data['start']['time'],
                'finished on: '. $data['finish']['time'],
                'duration: '. $data['duration'],
                'sequential 60s: '. $data['sequential'],
                'memory started: '. $this->testconvertMemoryUsage($data['start']['memory']),
                'memory finish: '. $this->testconvertMemoryUsage($data['finish']['memory']),
                'memory usage: '. $this->testconvertMemoryUsage($data['finish']['memory'] - $data['start']['memory']),
                'memory peak: '. $data['peak_memory'],
            ];

            $this->show($output);
        }

        public function testconvertMemoryUsage($size)
        {
            if (!$size || $size < 1) return 0;

            $unit=array('b','kb','mb','gb','tb','pb');
            return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
        }
    }

    // Alias for LodestoneAPI()
    class_alias('Viion\Lodestone\API', 'Viion\Lodestone\LodestoneAPI');
    class_alias('Viion\Lodestone\API', 'API\Lodestone\API');
    class_alias('Viion\Lodestone\API', 'LodestoneAPI');
    class_alias('Viion\Lodestone\API', 'API\API');
    class_alias('Viion\Lodestone\API', '_Shared\Lodestone\API');


    /*  Lodestone
     *  ---------
     */
    class Lodestone extends Parser
    {
        use Funky;
        use Config;

        // Construct
        function __construct() { }

        //------------------------------
        // Lodestone pages
        //------------------------------

        // Topics
        public function getTopics()
        {
            $this->log(__LINE__, '[Lodestone] getTopics()');

            // Parse
            $this->getSource($this->URL['lodestone']['topic']);

            // Find what we need
            $Found = $this->findAll('topics_list_inner', NULL, 'right_cont clearfix', false);

            // Temp Array
            $Array = [];

            // Total topics
            $Array['total'] = count($Found);

            // Default icon
            $Icon = 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_topics.png';

            // Loop through found to get data
            $Data = [];
            foreach($Found as $i => $D)
            {
                $Data[$i]['time']   = trim(explode(",", explode("(", $D[3])[2])[0]);
                $Data[$i]['url']    = trim(str_ireplace("/lodestone/topics/", null, $this->URL['lodestone']['topic']) . explode('&quot;', explode("&gt;", $D[6])[0])[1]);
                $Data[$i]['title']  = trim(explode("(", iconv("UTF-8", "ASCII//TRANSLIT", trim(strip_tags(html_entity_decode($D[6])))))[0]);
                $Data[$i]['image']  = trim(explode('&quot;', $D[8])[3]);
                $Data[$i]['icon']   = $Icon;
            }

            $Array['data'] = $Data;

            // Return topics
            return $Array;
        }

        // Notices
        public function getNotices()
        {
            $this->log(__LINE__, '[Lodestone] getNotices()');

            // Default icon
            $Icon = 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_info.png';

            // Run base parse
            $Array = $this->baseParse($this->URL['lodestone']['notices'], $Icon);

            // Return array
            return $Array;
        }

        // Maintenance issues
        public function getMaintenance()
        {
            $this->log(__LINE__, '[Lodestone] getMaintenance()');

            // Default icon
            $Icon = 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_maintenance.png';

            // Run base parse
            $Array = $this->baseParse($this->URL['lodestone']['maintenance'], $Icon);

            // Return array
            return $Array;
        }

        // Updates
        public function getUpdates()
        {
            $this->log(__LINE__, '[Lodestone] getUpdates()');

            // Default icon
            $Icon = 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_update.png';

            // Run base parse
            $Array = $this->baseParse($this->URL['lodestone']['updates'], $Icon);

            // Return array
            return $Array;
        }

        // Status issues
        public function getStatus()
        {
            $this->log(__LINE__, '[Lodestone] getStatus()');

            // Default icon
            $Icon = 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_obstacle.png';

            // Run base parse
            $Array = $this->baseParse($this->URL['lodestone']['status'], $Icon);

            // Return array
            return $Array;
        }
		
		/** Worldstatus
		 * Returs array Array("Datacenter" => "Server" = "Status")
		 * for example:
		 * Array
		 * (
		 *     [Elemental] => Array
		 *         (
		 *             [Aegis] => Online
		 *             [Atomos] => Online
		 *             [Carbuncle] => Online
		 *             [Garuda] => Online
		 *             [Gungnir] => Online
		 *             [Kujata] => Online
		 *             [Ramuh] => Online
		 *             [Tonberry] => Online
		 *             [Typhon] => Online
		 *             [Unicorn] => Online
		 *         )
		 * 
		 *     [Gaia] => Array
		 *         (
		 *             [Alexander] => Online
		 *             [Bahamut] => Online
		 *             [Durandal] => Online
		 *             [Fenrir] => Online
		 *		       ...
		 * @return array
		 */
		
		public  function getWorldstatus(){
			// Get Data from URL
			\phpQuery::newDocumentFileHTML($this->URL['lodestone']['worldstatus']);
			$dataArray = [];
			// Loop it
			foreach(pq('#server_status div.area_body') as $node){
				$pqNode = pq($node);
				$datacenter = trim(str_replace("Data Center: ", "",$pqNode->find('div.text-headline:first')->text()));
				$pqTable = $pqNode->next('div.area_inner_header')->find('table');
				foreach($pqTable->find('tr.worldstatus_1') as $tableRow){
					$pqTableRow = pq($tableRow);
					$server = trim($pqTableRow->find('td:first>div')->text());
					$status = trim($pqTableRow->find('td:last>span')->text());
					$dataArray[$datacenter][$server] = $status;
				}
			}
			return $dataArray;
		}

        // Handles the main parsing for lodestone pages that are categories.
        private function baseParse($url, $icon)
        {
            $this->log(__LINE__, '[Lodestone] baseParse()', [$url, $icon]);

            $this->getSource($url);

            // Find what we need
            $Found = $this->findAll('news_list clearfix', NULL, 'button bt_more', false);

            // Temp Array
            $Array = [];

            // Total topics
            $Array['total'] = count($Found);

            // Loop through found to get data
            foreach($Found as $i => $D)
            {
                $Temp = [];

                // Icon is fixed
                $Temp['icon'] = $icon;

                // Loop through and parse
                foreach($D as $line)
                {
                    // find time
                    if (stripos($line, 'ldst_strftime') !== false)
                    {
                        $Temp['time'] = trim($line);
                        $Temp['time'] = explode('(', $Temp['time'])[2];
                        $Temp['time'] = explode(',', $Temp['time'])[0];
                    }

                    // find tag
                    if (stripos($line, 'class=&quot;tag&quot;') !== false)
                    {
                        $Temp['tag'] = $this->strip_html($line);
                    }

                    // Find ID and Title
                    if (stripos($line, 'class=&quot;link&quot;') !== false)
                    {
                        $Temp['message'] = $this->strip_html($line);
                        $Temp['url'] = explode('&quot;', $line)[1];
                        $Temp['url'] = $this->URL['base'] . $Temp['url'];
                        $Temp['id'] = trim(explode('/', $Temp['url'])[6]);
                    }
                }

                // Add to array
                $Array['data'][] = $Temp;
            }

            return $Array;
        }

        //------------------------------
        // Lodestone forums
        //------------------------------

        public function getDevPosts()
        {
            $this->log(__LINE__, '[Lodestone] getDevPosts()');

            // Parse
            $this->getSource($this->URL['lodestone']['forums']);

            // Find what we need
            $Found = $this->findAll('avatarcontent floatcontainer widget_post_bit', null, 'class=&quot;time&quot;', false);

            // Temp Array
            $Array = [];

            // Total topics
            $Array['total'] = count($Found);

            // Loop through found to get data
            foreach($Found as $i => $D)
            {
                $Temp = [];

                // Grab generic data
                foreach($D as $ii => $line)
                {
                    // Find user and avatar
                    if (stripos($line, 'comments_member_avatar_link') !== false)
                    {
                        $var = explode('&quot;', $line)[3];
                        $var = explode('/', $var)[1];
                        $var = explode('-', $var);
                        $var[1] = explode('?', $var[1]);

                        $Temp['user']['id'] = trim($var[0]);
                        $Temp['user']['name'] = trim($var[1][0]);

                        // avatar
                        $var = $D[$ii + 1];
                        $Temp['user']['avatar'] = str_ireplace("'", "%27", 'http://forum.square-enix.com/ffxiv/'. $this->getAttribute('src', $var));
                    }

                    // Find topic title and url
                    if (stripos($line, 'widget_post_header') !== false)
                    {
                        // Title
                        $var = trim($line);
                        $Temp['topic']['title'] = $this->strip_html($var);

                        // url
                        $var = $this->getAttribute('href', $var);
                        $Temp['topic']['url'] = 'http://forum.square-enix.com/ffxiv/'. $var;

                        // topic ID
                        $var = explode('/', $var)[1];
                        $var = trim(explode('-', $var)[0]);
                        $Temp['topic']['id'] = $var;

                        // Post id
                        $var = explode('#', $Temp['topic']['url']);
                        if (isset($var[1]))
                        {
                            $var = $var[1];
                            $var = str_ireplace('post', null, $var);
                            $Temp['topic']['post_id'] = trim($var);
                        }
                        else
                        {
                            // New topic, not a reply post, so post is same as topic id
                            $Temp['topic']['post_id'] = $Temp['topic']['id'];
                        }
                    }

                    // Find time
                    if (stripos($line, 'class=&quot;time&quot;') !== false)
                    {
                        $var = explode(' ', $line);
                        $var['date'] = $var[0];

                        // Replace 'today' with actually today...
                        if (trim($var['date']) == 'Today')
                        {
                            $var['date'] = date('m-d-Y', time());
                        }

                        // Replace 'yesterday' with actually yesterday ...
                        if (trim($var['date']) == 'Yesterday')
                        {
                            $var['date'] = date('m-d-Y', (time() - (60*60*24)));
                        }

                        // Change date format
                        $var['date'] = str_ireplace('-', '/', $var['date']);

                        // Get time
                        $var['format'] = $var[1] .' '. $var[2] .'-'. $var[3];
                        $var['format'] = $this->strip_html($var['format']);
                        $var['format'] = explode('-', $var['format']);

                        // Create string to parse
                        $timeString = $this->strip_html($var['date'] .' '. implode(' ', $var['format']));
                        $timeUnix = strtotime($timeString);

                        $Temp['time']['string'] = $timeString;
                        $Temp['time']['unix'] = $timeUnix;
                    }
                }

                // Getting the message is a little more complicated as
                // it can be across multiple lines, so going to do
                // another loop just specifically for the message
                $message = [];
                $addToMessage = false;
                foreach($D as $ii => $line)
                {
                    // If we've found the line with the message, enable
                    // addToMessage to begin populating the message array
                    if (stripos($line, 'widget_post_content') !== false)
                    {
                        $addToMessage = true;
                    }

                    // If add to message is true, add the line to the message
                    if ($addToMessage)
                    {
                        $message[] = $line;
                    }

                    // If we've at the 'widget_post_header' tag, the message has ending
                    if (stripos($line, 'widget_post_header') !== false)
                    {
                        $addToMessage = false;
                        break;
                    }
                }

                // Remove last element
                array_pop($message);

                // Make 1 line
                $message = implode(' ', $message);

                // Remove html
                $message = $this->strip_html($message);
                $Temp['topic']['message'] = $message;

                // Make sure dev post has a message
                if (strlen($message) > 5)
                {
                    // Add to array
                    $Array['data'][] = $Temp;
                }
            }

            // Return
            return $Array;
        }
    }


    // Alias for Lodestone()
    class_alias('Viion\Lodestone\Lodestone', 'Lodestone');
    class_alias('Viion\Lodestone\Lodestone', 'Lodestone\Lodestone');
    /*  Social
     *  ---------
     */
    class Social extends Parser
    {
        use Funky;
        use Config;

        // Construct
        function __construct() { }

        //------------------------------
        // Lodestone pages
        //------------------------------

        // Youtube videos
        function getYoutubeVideos($max = 20, $simple = true)
        {
            $this->log(__LINE__, '[Social] getYoutubeVideos()', [$max, $simple]);

            // Get key
            $gkey = $this->getGoogleAPIKey();

            // Set urls
            $channelURL = $this->URL['youtube']['channels'];
            $playlistURL = $this->URL['youtube']['playlists'];

            // Set social ids
            $socialID = $this->URL['social']['youtube'];

            // Replace
            $channelURL = str_ireplace(['{channel}', '{key}'], [$socialID, $gkey], $channelURL);

            // Get json
            $this->log(__LINE__, 'getYoutubeVideos() > file_get_contents() - URL: '. $channelURL);
            $json = json_decode(file_get_contents($channelURL), true);

            // Make sure we got somethingz and it not empty
            if ($json)
            {
                // Get the playlist id
                $playlistID = $json['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

                // Replace
                $playlistURL = str_ireplace(['{max}', '{playlistID}', '{key}'], [$max, $playlistID, $gkey], $playlistURL);

                // Get json
                $this->log(__LINE__, 'getYoutubeVideos() > file_get_contents() - URL: '. $playlistURL);
                $json = json_decode(file_get_contents($playlistURL), true);

                // Make sure we got somethingz and it not empty
                if ($json)
                {
                    // If simple is true, we will just get very specific information
                    // else if simple is not true, we will return the entire json result
                    if ($simple)
                    {
                        $this->log(__LINE__, 'getYoutubeVideos() - Generating simple array');

                        // Temp Array
                        $youtubeUploadsArray = [];

                        // Set total
                        $youtubeUploadsArray['total'] = $json['pageInfo']['totalResults'];
                        $youtubeUploadsArray['fetched'] = $json['pageInfo']['resultsPerPage'];

                        // Prevent PHP notices
                        $youtubeUploadsArray['data'] = [];

                        // Loop
                        foreach($json['items'] as $upload)
                        {
                            // Parse time
                            $unixTime = strtotime($upload['snippet']['publishedAt']);

                            // Set temp array data
                            $arr =
                            [
                                'time'          => $unixTime,
                                'published'     => $upload['snippet']['publishedAt'],
                                'title'         => $upload['snippet']['title'],
                                'description'   => $upload['snippet']['description'],

                                'video' =>
                                [
                                    'id'        => $upload['snippet']['resourceId']['videoId'],
                                    'url'       => 'https://www.youtube.com/watch?v='. $upload['snippet']['resourceId']['videoId'],
                                ],

                                'thumbnails' =>
                                [
                                   'default'    => $upload['snippet']['thumbnails']['default'],
                                   'medium'     => isset($upload['snippet']['thumbnails']['medium']) ? $upload['snippet']['thumbnails']['medium'] : null,
                                   'high'       => isset($upload['snippet']['thumbnails']['high']) ? $upload['snippet']['thumbnails']['high'] : null,
                                   'standard'   => isset($upload['snippet']['thumbnails']['standard']) ? $upload['snippet']['thumbnails']['standard'] : null,
                                   'maxres'     => isset($upload['snippet']['thumbnails']['maxres']) ? $upload['snippet']['thumbnails']['maxres'] : null,
                                ]
                            ];

                            $youtubeUploadsArray['data'][] = $arr;
                        }

                        // Return
                        $this->log(__LINE__, 'getYoutubeVideos() - Finished');
                        return $youtubeUploadsArray;
                    }
                    else
                    {
                        // return
                        return $json;
                    }
                }
                else
                {
                    // No json return
                    $this->log(__LINE__, '[Social] getYoutubeVideos() - Failure: No json on related playlist id');
                }
            }
            else
            {
                // No json return
                $this->log(__LINE__, '[Social] getYoutubeVideos() - Failure: No json on social channel id');
            }
        }

        // Tweetz (this can fail often on the curl, cus twitter fking sucks)
        function getTweets($max = 20, $language = 'en')
        {
            $this->log(__LINE__, '[Social] getTweets()', [$max, $language]);

            // URL to parse
            $url = $this->URL['twitter'][$language];

            // Get source
            $Source = $this->getSource($url);
            $this->log(__LINE__, '[Social] getTweets() - Obtained source, parsing...');

            // Get tweets source
            $TweetsFound = $this->findAll('data-component-term', NULL, 'ProfileTweet-actionList u-cf js-actions', false);

            // Temp array
            $Tweets = [];

            // Set total
            $Tweets['total'] = count($TweetsFound);

            // Loop
            foreach($TweetsFound as $i => $tf)
            {
                $temp = [];

                // Loop through each line
                foreach($tf as $ii => $line)
                {
                    // Check for ID
                    if (stripos($line, 'data-tweet-id') !== false)
                    {
                        $temp['id'] = trim(explode('&quot;', $line)[1]);
                    }

                    // Check for tweet context
                    if (stripos($line, 'ProfileTweet-text') !== false)
                    {
                        $temp['message'] = '&lt;p '. $tf[$ii + 1];
                        $temp['message'] = $this->strip_html($temp['message']);
                        $temp['message'] = str_ireplace('http', ' http', $temp['message']);
                        $temp['message'] = trim(str_ireplace('  ', ' ', $temp['message']));
                    }

                    // Check for avatar
                    if (stripos($line, 'js-action-profile-avatar') !== false)
                    {
                        $temp['avatar']['normal'] = explode(' ', $line)[3];
                        $temp['avatar']['normal'] = trim(str_ireplace(['src=', '&quot;'], null, $temp['avatar']['normal']));

                        // Other sizes
                        $temp['avatar']['bigger']   = trim(str_ireplace('_normal', '_bigger', $temp['avatar']['normal']));
                        $temp['avatar']['mini']     = trim(str_ireplace('_normal', '_mini', $temp['avatar']['normal']));
                        $temp['avatar']['original'] = trim(str_ireplace('_normal', null, $temp['avatar']['normal']));
                    }

                    // Check for time
                    if (stripos($line, 'data-time=') !== false)
                    {
                        $temp['time']['unix'] = trim(str_ireplace(['data-time=', '&quot;'], null, $line));
                        $temp['time']['stamp'] = date('F j, Y, g:i a', $temp['time']['unix']);
                    }

                    // Check if retweet
                    if (stripos($line, 'retweeted') !== false)
                    {
                        $temp['retweet'] = true;
                    }

                    // Check for profile name
                    if (stripos($line, 'ProfileTweet-fullnam') !== false)
                    {
                        $temp['profile'] = $this->strip_html($line);
                    }

                    // Check for screen name and user id
                    if (stripos($line, 'data-screen-name') !== false)
                    {
                        $data = explode("&quot;", $line);
                        $temp['user']['handle']     = trim($data[1]);
                        $temp['user']['profile']    = trim($data[3]);
                        $temp['user']['id']         = trim($data[5]);
                    }
                }

                // Create link
                $temp['url'] = 'http://twitter.com/'. $temp['user']['handle'] .'/status/'. $temp['id'];

                // Append temp to tweets
                $Tweets['data'][] = $temp;
            }

            // Return
            $this->log(__LINE__, '[Social] getTweets() - Returning Tweets');
            return $Tweets;
        }
    }


    /*  Character
     *  ---------
     */
    class Character extends Parser
    {
        use Funky;
        use Config;

        private $ID;
        private $Lodestone;
        private $Name;
        private $NameClean;
        private $Server;
        private $Title;
        private $Avatars;
        private $Portrait;
        private $Legacy;
        private $Race;
        private $Clan;
        private $Nameday;
        private $Guardian;
        private $Company;
        private $FreeCompany;
        private $City;
        private $Biography;
        private $Stats;
        private $Gear;
        private $Minions;
        private $Mounts;
        private $ClassJob;
        private $Validated = true;
        private $Errors = array();

        #-------------------------------------------#
        # FUNCTIONS                                 #
        #-------------------------------------------#

        // ID
        public function setID($ID, $URL = NULL)
        {
            $this->ID = $ID;
            $this->Lodestone = $URL;
        }
        public function getID() { return $this->ID; }
        public function getLodestone() { return $this->Lodestone; }
        public function getLodestoneID() { return $this->LodestoneID; }

        // NAME + SERVER
        public function setNameServer($String)
        {
            $name = null;
            $server = null;

            // Dirty temp fix
            foreach($String as $i => $s)
            {
                if (stripos($s, '(') !== false)
                {
                    $server = $s;
                    $name = $String[$i - 1];
                }
            }

            $this->Name         = str_ireplace("&#39;", "'", trim($name));
            $this->Server       = trim(str_ireplace(["(", ")"], null, $server));
            $this->NameClean    = preg_replace('/[^a-z]/i', '', strtolower($this->Name));
        }
        public function getName() { return $this->Name; }
        public function getServer() { return $this->Server; }
        public function getNameClean() { return$this->NameClean; }

        // TITLE
        public function setTitle($String)
        {
            $this->Title = strip_tags(htmlspecialchars_decode(trim($String[0]), ENT_QUOTES));
        }
        public function getTitle() { return $this->Title; }

        // AVATAR
        public function setAvatar($String)
        {
            $String = $String[2];
            if (isset($String))
            {
                $this->Avatars['50'] = trim(explode('&quot;', $String)[1]);
                $this->Avatars['64'] = str_ireplace("50x50", "64x64", $this->Avatars['50']);
                $this->Avatars['96'] = str_ireplace("50x50", "96x96", $this->Avatars['50']);
            }
        }
        public function getAvatar($Size = null) { if (!$Size) $Size = 96; return $this->Avatars[$Size]; }

        // PORTRAIT
        public function setPortrait($String)
        {
            if (isset($String))
            {
                $portraitURL = trim(explode('&quot;', $String[1])[1]);
                $this->Portrait['small'] = $portraitURL;
                $this->Portrait['big'] = str_replace('264x360', '640x873', $portraitURL);
            }
        }
        public function getPortrait($Size = null) { if (!$Size) $Size = 'small'; return $this->Portrait[$Size]; }

        // RACE + CLAN
        public function setRaceClan($String)
        {
            if (isset($String))
            {
                $String         = explode("/", $String);
                $this->Clan     = htmlspecialchars_decode(trim($String[1]), ENT_QUOTES);
                $this->Race     = htmlspecialchars_decode(trim($String[0]), ENT_QUOTES);
                $GenderUnicode  = $this->uniord(htmlspecialchars_decode(trim($String[2]), ENT_QUOTES));
                $this->Gender   = ($GenderUnicode == 9792) ? 'female' : 'male';
            }
        }
        public function getRace() { return $this->Race; }
        public function getClan() { return $this->Clan; }
        public function getGender() { return $this->Gender; }

        // LEGACY
        public function setLegacy($String) { $this->Legacy = $String; }
        public function getLegacy() { return $this->Legacy; }

        public function setNamedayCityCompanyFC($String)
        {
            foreach($String as $i => $line)
            {
                if (stripos($line, 'Nameday') !== false)
                {
                    $offset = ($i + 1);
                    $this->Nameday = $this->strip_html($String[$offset]);
                }

                if (stripos($line, 'Guardian') !== false)
                {
                    $offset = ($i + 1);
                    $this->Guardian = $this->strip_html($String[$offset]);
                }

                if (stripos($line, 'City-state') !== false)
                {
                    $offset = ($i + 1);
                    $this->City = $this->strip_html($String[$offset]);
                }

                if (stripos($line, 'Grand Company') !== false)
                {
                    $offset = ($i + 1);
                    $data = explode('/', $this->strip_html($String[$offset]));
                    $this->Company['name'] = $data[0];
                    $this->Company['rank'] = $data[1];
                }

                if (stripos($line, 'Free Company') !== false)
                {
                    $offset = ($i + 1);
                    $this->FreeCompany = explode("/", $String[$offset]);
                    $this->FreeCompany = trim($this->FreeCompany[3]);

                    break;
                }
            }
        }

        public function getNameday()        { return $this->Nameday; }
        public function getGuardian()       { return $this->Guardian; }
        public function getCompanyName()    { return $this->Company['name']; }
        public function getCompanyRank()    { return $this->Company['rank']; }
        public function getFreeCompany()    { return $this->FreeCompany; }

        // CITY
        public function setCity($String) { $this->City = htmlspecialchars_decode(trim($String[1]), ENT_QUOTES); }
        public function getCity() { return $this->City; }

        // BIOGRAPHY
        public function setBiography($String) { $this->Biography = trim($String[0]); }
        public function getBiography() { return $this->Biography; }

        // HP + MP + TP
        public function setHPMPTP($String)
        {
			// Setting default values
			$this->Stats['core'] = array(
				'hp' => 0,
				'mp' => 0,
				'cp' => 0,
				'gp' => 0,
				'tp' => 0,
			);
			
            $this->Stats['core']['hp'] = trim($String[0]);
            $this->Stats['core']['tp'] = trim($String[2]);
			// Place Parsed Data to correct classtype
			switch($this->Stats['activeType']){
				case 'crafter':
					$points = 'cp';
					break;
				case 'gatherer':
					$points = 'gp';
					break;
				default:
					$points = 'mp';
			}
            $this->Stats['core'][$points] = trim($String[1]);
        }

        // Stats
        public function setStats($String)
        {
            $this->Stats['attributes']['strength']          = trim(filter_var($String[0][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['attributes']['dexterity']         = trim(filter_var($String[0][5], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['attributes']['vitality']          = trim(filter_var($String[0][6], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['attributes']['intelligence']      = trim(filter_var($String[0][7], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['attributes']['mind']              = trim(filter_var($String[0][8], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['attributes']['piety']             = trim(filter_var($String[0][9], FILTER_SANITIZE_NUMBER_INT));

            $this->Stats['elemental']['fire']               = trim(filter_var($String[1][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['elemental']['ice']                = trim(filter_var($String[1][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['elemental']['wind']               = trim(filter_var($String[1][5], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['elemental']['earth']              = trim(filter_var($String[1][6], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['elemental']['lightning']          = trim(filter_var($String[1][7], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['elemental']['water']              = trim(filter_var($String[1][8], FILTER_SANITIZE_NUMBER_INT));

            $this->Stats['offense']['accuracy']             = trim(filter_var($String[2][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['offense']['critical hit rate']    = trim(filter_var($String[2][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['offense']['determination']        = trim(filter_var($String[2][5], FILTER_SANITIZE_NUMBER_INT));

            $this->Stats['defense']['defense']              = trim(filter_var($String[3][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['defense']['parry']                = trim(filter_var($String[3][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['defense']['magic defense']        = trim(filter_var($String[3][5], FILTER_SANITIZE_NUMBER_INT));

            $this->Stats['physical']['attack power']        = trim(filter_var($String[4][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['physical']['skill speed']         = trim(filter_var($String[4][4], FILTER_SANITIZE_NUMBER_INT));

            // 5th one switches between different types of classes
			// Setting defaults to avoid undefined indexes (13.01.2014 @JohnRamboTSQ)
			$this->Stats['spell'] = [
				'attack magic potency' => null,
				'healing magic potency' => null,
				'spell speed' => null
			];
			$this->Stats['crafting'] = [
				'craftsmanship' => null,
				'control' => null
			];
			$this->Stats['gathering'] = [
				'gathering' => null,
				'perception' => null
			];
			$this->Stats['activeType'] = null;
            if (stripos($String[5][3], 'Craftsmanship') !== false)
            {
                $this->Stats['crafting']['craftsmanship']       = trim(filter_var($String[5][3], FILTER_SANITIZE_NUMBER_INT));
                $this->Stats['crafting']['control']             = trim(filter_var($String[5][4], FILTER_SANITIZE_NUMBER_INT));

                $last = 7;
				
				$this->Stats['activeType'] = 'crafter';
            }
            else if (stripos($String[5][3], 'Gathering') !== false)
            {
                $this->Stats['gathering']['gathering']          = trim(filter_var($String[5][3], FILTER_SANITIZE_NUMBER_INT));
                $this->Stats['gathering']['perception']         = trim(filter_var($String[5][4], FILTER_SANITIZE_NUMBER_INT));

                $last = 7;
				$this->Stats['activeType'] = 'gatherer';
            }
            else
            {
                $this->Stats['spell']['attack magic potency']   = trim(filter_var($String[5][3], FILTER_SANITIZE_NUMBER_INT));
                $this->Stats['spell']['healing magic potency']  = trim(filter_var($String[5][4], FILTER_SANITIZE_NUMBER_INT));
                $this->Stats['spell']['spell speed']            = trim(filter_var($String[5][5], FILTER_SANITIZE_NUMBER_INT));

                $this->Stats['pvp']['morale']                   = trim(filter_var($String[7][3], FILTER_SANITIZE_NUMBER_INT));

                $last = 8;
            }

            $this->Stats['resists']['slow']                 = trim(filter_var($String[6][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['silence']              = trim(filter_var($String[6][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['blind']                = trim(filter_var($String[6][5], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['poison']               = trim(filter_var($String[6][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['stun']                 = trim(filter_var($String[6][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['sleep']                = trim(filter_var($String[6][5], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['bind']                 = trim(filter_var($String[6][5], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['heavy']                = trim(filter_var($String[6][5], FILTER_SANITIZE_NUMBER_INT));

            $this->Stats['resists']['slashing']             = trim(filter_var($String[7][3], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['piercing']             = trim(filter_var($String[7][4], FILTER_SANITIZE_NUMBER_INT));
            $this->Stats['resists']['blunt']                = trim(filter_var($String[7][5], FILTER_SANITIZE_NUMBER_INT));
        }

        // GET STAT FUNC
        public function getStat($Type, $Attribute) { if (isset($this->Stats[$Type])) { return $this->Stats[$Type][$Attribute]; } else { return 0; }}
        public function getStats() { return $this->Stats; }

        // ACTIVE CLASS + LEVEL
        public function setActiveClassLevel($Arr)
        {
            // set active level
            $this->Stats['active']['level'] = trim(filter_var($Arr[1][1], FILTER_SANITIZE_NUMBER_INT));
        }

        // GEAR
        public function setGear($Array)
        {
            $this->log(__LINE__, '... set gear');

            $this->Gear['slots'] = count($Array);
            $GearArray = NULL;

            // Get ID List
            //$this->log(__LINE__, '... getting items json from XIVPads');

            // THIS FILE SHOULD REALLY BE HOSTED LOCALLY
            // OR MEMCACHED
            // OR REDIS
            // OR unoe

            $ItemIDArray = [];
            if (file_exists(__DIR__.'/items.json'))
            {
                $ItemIDArray = json_decode(file_get_contents(__DIR__."/items.json"), true);
                $this->log(__LINE__, '... >> obtained item json locally');
            }

            // Loop through gear equipped
            $Main = NULL;
            $this->log(__LINE__, '... big loop');
            foreach($Array as $A)
            {
                // Temp array
                $Temp = array();
                //Show($A);

                // Loop through data
                $this->log(__LINE__, '... big loop 2');
                foreach($A as $i => $Line)
                {
                    // Name / Id
                    if (stripos($Line, 'item_name') !== false && stripos($Line, 'item_name_right') === false)
                    {
                        // Name
                        $index = ($i + 2);
                        $itemName = $A[$index];
                        $itemName = strip_tags(html_entity_decode($itemName));
                        $itemName = str_ireplace('">', null, $itemName);
                        $itemName = str_ireplace("&#39;", "'", trim($itemName));
                        $Temp['name'] = $itemName;

                        if (empty($Temp['name']))
                        {
                            Show($A);
                        }

                        // Get item ID
                        $Temp['id'] = null;
                        $itemNameHashed = md5(strtolower(preg_replace('/[^a-z-]/i', null, $itemName)));
                        if (isset($ItemIDArray[$itemNameHashed]))
                        {
                            $itemID = $ItemIDArray[$itemNameHashed];
                            $Temp['id'] = $itemID;
                            $Temp['xivdb'] = 'http://xivdb.com/?item/'. $itemID .'/'. str_ireplace(' ', '-', $itemName);
                        }
                        else
                        {
                            //Show($itemNameHashed);
                            //Show($itemName);
                            //Show(strtolower(preg_replace('/[^a-z-]/i', null, $itemName)));
                        }
                    }

                    // If glamoured
                    if (stripos($Line, 'mirageitem_ic') !== false)
                    {
                        $index = ($i + 1);
                        $itemGlamourIcon = $A[$index];
                        $itemGlamourIcon = trim(explode('&quot;', $itemGlamourIcon)[1]);
                        $Temp['glamour']['icon'] = $itemGlamourIcon;

                        $index = ($i + 4);
                        $itemGlamourName = $A[$index];
                        $itemGlamourName = strip_tags(html_entity_decode($itemGlamourName));
                        $itemGlamourName = str_ireplace("&#39;", "'", trim($itemGlamourName));
                        $Temp['glamour']['name'] = $itemGlamourName;

                        // Get item ID
                        $Temp['glamour']['id'] = null;
                        $itemGlamourNameHashed = md5(strtolower(preg_replace('/[^a-z-]/i', null, $itemGlamourName)));
                        if (isset($ItemIDArray[$itemGlamourNameHashed]))
                        {
                            $itemGlamourID = $ItemIDArray[$itemGlamourNameHashed];
                            $Temp['glamour']['id'] = $itemGlamourID;
                            $Temp['glamour']['xivdb'] = 'http://xivdb.com/?item/'. $itemGlamourID .'/'. str_ireplace(' ', '-', $itemGlamourName);
                        }
                        else
                        {
                            //Show($itemNameHashed);
                            //Show($itemName);
                            //Show(strtolower(preg_replace('/[^a-z-]/i', null, $itemName)));
                        }
                    }

                    // Category / Slot / Class
                    if (stripos($Line, 'category_name') !== false)
                    {
                        // Category
                        $index = ($i);
                        $itemCategory = $A[$i];
                        $itemCategory = strip_tags(html_entity_decode($itemCategory));
                        $itemCategory = str_ireplace("&#39;", "'", trim($itemCategory));
                        $Temp['category'] = htmlspecialchars_decode($itemCategory);

                        // Slot
                        $itemSlot = $itemCategory;
                        if (
                            strpos($itemSlot, " Arm") !== false ||
                            strpos($itemSlot, " Arms") !== false ||
                            strpos($itemSlot, " Grimoire") !== false ||
                            strpos($itemSlot, " Primary Tool") !== false
                        )
                        {
                            $ClassJob = strtolower(explode("'", ($itemSlot))[0]);
                            $itemSlot = 'Main';
                        }
                        // If tool
                        if (strpos($itemSlot, " Secondary Tool") !== false)
                        {
                            $ClassJob = strtolower(explode("'", ($itemSlot))[0]);
                            $itemSlot = 'tool';
                        }

                        $Temp['slot'] = strtolower($itemSlot);
                    }

                    // Icon
                    if (stripos($Line, 'socket_64') !== false)
                    {
                        $index = ($i + 1);
                        $itemIcon = $A[$index];
                        $itemIcon = trim(explode('&quot;', $itemIcon)[1]);
                        $Temp['icon'] = $itemIcon;
                    }

                    // Item level
                    if (stripos($Line, 'Item Level') !== false)
                    {
                        $index = ($i);
                        $itemLevel = $A[$index];
                        $itemLevel = strip_tags(html_entity_decode($itemLevel));
                        $itemLevel = filter_var($itemLevel, FILTER_SANITIZE_NUMBER_INT);
                        $Temp['ilevel'] = $itemLevel;
                    }

                    // Level
                    if (stripos($Line, 'gear_level') !== false)
                    {
                        $index = ($i);
                        $itemLevel = $A[$index];
                        $itemLevel = strip_tags(html_entity_decode($itemLevel));
                        $itemLevel = filter_var($itemLevel, FILTER_SANITIZE_NUMBER_INT);
                        $Temp['level'] = $itemLevel;
                    }

                    // ID Lodestone
                    if (stripos($Line, 'bt_db_item_detail') !== false)
                    {
                        $index = ($i + 1);
                        $Data = trim(str_ireplace(array('>', '"'), NULL, html_entity_decode(preg_match("/\/lodestone\/playguide\/db\/item\/([a-z0-9]{11})\//", $A[$index], $matches))));

                        // TODO : Fix properly, matches is returining false, likely index incorrect.
                        $Temp['id_lodestone'] = null;
                        if (isset($matches[1]))
                        {
                            $Temp['id_lodestone'] = $matches[1];
                        }
                    }

                    // Cannot equip
                    if (stripos($Line, 'Cannot equip gear to') !== false)
                    {
                        $Data = trim(str_ireplace(array('>', '"'), NULL, strip_tags(html_entity_decode($Line))));
                        $Temp['no_equip'] = htmlspecialchars_decode(trim(str_replace(['Cannot equip gear to', '.'], null,  $Data)), ENT_QUOTES);
                        $Temp['no_equip_slots'] = explode(" ", str_ireplace([".", ", or", ", and", "Cannot equip gear to ", ","], null, $Data));
                        $Temp['no_equip_count'] = count($Temp['no_equip_slots']);
                    }
                }
                $this->log(__LINE__, '... big loop 2 /end');

                // Slot manipulation, mainly for rings
                $Slot = $Temp['slot'];
                if (isset($GearArray['slots'][$Slot]) && $Slot == 'ring') { $Slot = $Slot . 2; }
                $Temp['slot'] = $Slot;

                // Add index
                $Temp['slot_id'] = array_search($Temp['slot'], $this->GearSlots);

                // Append array
                $GearArray['numbers'][$Temp['slot_id']] = $Temp;
                $GearArray['slots'][$Slot] = $Temp;
            }
            $this->log(__LINE__, '... big loop /end');

            // Set Gear
            $this->Gear['equipped'] = $GearArray;

            // Set Active Class
            $ReplaceArray = ['Two-Handed ', 'One-Handed'];
            $ClassJob = str_ireplace($ReplaceArray, NULL, $ClassJob);

            $this->Stats['active']['class'] = trim($ClassJob);
            if (isset($this->Gear['equipped']['slots']['soul crystal'])) { $this->Stats['active']['job'] = str_ireplace("Soul of the ", NULL, $this->Gear['equipped']['slots']['soul crystal']); }
        }
        public function getGear()           { return $this->Gear; }
        public function getEquipped($Type)  { return $this->Gear['equipped'][$Type]; }
        public function getSlot($Slot)      { return isset($this->Gear['equipped']['slots'][$Slot]) ? $this->Gear['equipped']['slots'][$Slot] : null; }
        public function getActiveClass()    { return $this->Stats['active']['class']; }
        public function getActiveJob()      { return isset($this->Stats['active']['job']) ? $this->Stats['active']['job'] : NULL; }
        public function getActiveLevel()    { return $this->Stats['active']['level']; }

        public function getItemLevelArray()     { return $this->Gear['item_level_array']; }
        public function getItemLevelTotal()     { return $this->Gear['item_level_total']; }
        public function getItemLevelAverage()   { return $this->Gear['item_level_average']; }

        // Item Level
        public function setItemLevel($GearSlots)
        {

            $this->log(__LINE__, '... set item level');

            // Remoove soul crystal as its not calculated in ilv
            unset($GearSlots[3]);

            // List of categories that have their item level duplicated
            $arrayOfDuplicatedGear =
            [
                "Pugilist's Arm",
                "Marauder's Arm",
                "Archer's Arm",
                "Lancer's Arm",
                "Rogue's Arms",
                "Two-handed Thaumaturge's Arm",
                "Two-handed Conjurer's Arm",
                "Arcanist's Grimoire"
            ];

            // Loop through gear to calculate item levels
            $itemLevels = [];
            $itemlevelsSlotID = [];
            foreach($GearSlots as $Slot)
            {
                // Get the gear
                $Gear = $this->getSlot($Slot);

                if ($Gear)
                {
                    // Get the item level
                    $itemLevel = $Gear['ilevel'];

                    // If category in array of duplicated gear, * 2 the item level
                    if (in_array($Gear['category'], $arrayOfDuplicatedGear)) $itemLevel = $itemLevel * 2;


                    // If item takes up multiple slots
                    if (isset($Gear['no_equip_count']))
                    {
                        // Multiply the item level by the number of slots it takes up + 1
                        // (as multi-slot gear always take up their own slot then some other slot)
                        $itemLevel = $itemLevel * ($Gear['no_equip_count'] + 1);
                    }
                }
                else
                {
                    $itemLevel = 0;
                }

                // Add item level
                $itemLevels[$Slot] = $itemLevel;

                // Insert as slot id
                $itemlevelsSlotID[array_search($Slot, $this->GearSlots)] = $itemLevel;
            }

            $this->Gear['item_level_array'] = $itemLevels;
            $this->Gear['item_level_slots'] = $itemlevelsSlotID;
            $this->Gear['item_level_total'] = array_sum($itemLevels);
            $this->Gear['item_level_average'] = floor($this->Gear['item_level_total'] / 13);
        }

        // MINIONS
        public function setMinions($Array)
        {
            // Pet array
            $Pets = array();

            // Loop through array
            $i = 0;
            foreach($Array as $A)
            {
                if (stripos($A, 'ic_reflection_box') !== false)
                {
                    $arr = array();
                    $arr['name'] = trim(explode('&quot;', $Array[$i])[5]);
                    $arr['icon'] = trim(explode('&quot;', $Array[$i + 1])[1]);
                    $arr['name'] = str_ireplace("&#39;", "'", html_entity_decode($arr['name']));

                    $Pets[] = $arr;
                }

                // Increment
                $i++;
            }

            // set pets
            $this->Minions = $Pets;
        }
        public function getMinions() { return $this->Minions; }

        // MOUNTS
        public function setMounts($Array)
        {
            // Mount array
            $Mounts = array();

            // Loop through array
            $i = 0;
            foreach($Array as $A)
            {
                if (stripos($A, 'ic_reflection_box') !== false)
                {
                    $arr = array();
                    $arr['name'] = trim(explode('&quot;', $Array[$i])[5]);
                    $arr['icon'] = trim(explode('&quot;', $Array[$i + 1])[1]);
                    $Mounts[] = $arr;
                    
                    if (strtolower($arr['name']) == 'legacy chocobo') {
                        $this->Legacy = true;
                    }
                }

                // Increment
                $i++;
            }

            // set Mounts
            $this->Mounts = $Mounts;
        }
        public function getMounts() { return $this->Mounts; }

        // CLASS + JOB
        public function setClassJob($Array)
        {
            // Temp array
            $Temp = array();

            // Loop through class jobs
            $i = 0;
            foreach($Array as $A)
            {
                // If class
                if(stripos($A, 'ic_class_wh24_box') !== false)
                {
                    $Icon   = isset(explode(" ", $A)[2]) ? explode('?', str_ireplace(array('"', 'src='), '', html_entity_decode(explode(" ", $A)[2])))[0] : null;
                    $Class  = strtolower(trim(strip_tags(html_entity_decode($Array[$i]))));
                    $Level  = trim(strip_tags(html_entity_decode($Array[$i + 1])));
                    $EXP    = trim(strip_tags(html_entity_decode($Array[$i + 2])));

                    if ($Class)
                    {
                        $arr = array(
                            'class' => $Class,
                            'icon'  => $Icon,
                            'level' => $Level,
                            'exp'   => array(
                                'current' => explode(" / ", $EXP)[0],
                                'max' => explode(" / ", $EXP)[1]
                            ),
                            'exp-current' => explode(" / ", $EXP)[0],
                            'exp-max' => explode(" / ", $EXP)[1],
                        );

                        if ($arr['level'] == '-') { $arr['level'] = 0; }
                        if ($arr['exp']['current'] == '-') { $arr['exp']['current'] = 0; }
                        if ($arr['exp']['max'] == '-') { $arr['exp']['max'] = 0; }
                        if ($arr['exp-current'] == '-') { $arr['exp-current'] = 0; }
                        if ($arr['exp-max'] == '-') { $arr['exp-max'] = 0; }

                        $Temp['numbered'][] = $arr;
                        $Temp['named'][$Class] = $arr;
                    }
                }

                // Increment
                $i++;
            }

            $this->ClassJob = $Temp;
        }
        public function getClassJob($class)
        {
            $type = 'named';
            if (is_numeric($class))
            {
                $type = 'numbered';
            }

            return $this->ClassJob[$type][$class];
        }

        public function getClassJobs($Specific = null)
        {
            $data = $this->ClassJob;

            if ($Specific) {
               $data = $this->ClassJob[$Specific];
            }

            return $data;
        }
        public function getClassJobsOrdered($Specific = null, $Order = false, $OrderBy = null, $ArrayType = null)
        {
            // OPrder
            if (strtolower($Order) == 'asc') { $Ascending = true; } else { $Ascending = false; }

            if (!$Specific) {
                $Specific = 'named';
            }

            // Get the jobs
            $ClassJobs = $this->getClassJobs($ArrayType)[$Specific];

            // Set order by
            if (!$OrderBy) { $OrderBy = 'level'; }

            // Get a specific job
            // Sort by value
            $this->sksort($ClassJobs, $OrderBy, $Ascending);

            // Return
            return $ClassJobs;
        }

        // VALIDATE
        public function validate()
        {
            $this->log(__LINE__, 'function: validate() - start');

            // Check Name
            if (!$this->Name)           { $this->Validated = false; $this->Errors[] = 'Name is false'; }
            if (!$this->Server)         { $this->Validated = false; $this->Errors[] = 'Server is false'; }
            if (!$this->ID)             { $this->Validated = false; $this->Errors[] = 'ID is false'; }
            if (!$this->Lodestone)      { $this->Validated = false; $this->Errors[] = 'Lodestone URL is false'; }
            if (!$this->Avatars['96'])  { $this->Validated = false; $this->Errors[] = 'Avatars is false'; }

            if (!$this->Portrait)       { $this->Validated = false; $this->Errors[] = 'Portrait is false'; }
            if (!$this->Race)           { $this->Validated = false; $this->Errors[] = 'Race is false'; }
            if (!$this->Clan)           { $this->Validated = false; $this->Errors[] = 'Clan is false'; }
            if (!$this->Nameday)        { $this->Validated = false; $this->Errors[] = 'Nameday is false'; }
            if (!$this->Guardian)       { $this->Validated = false; $this->Errors[] = 'Guardian is false'; }
            if (!$this->City)           { $this->Validated = false; $this->Errors[] = 'City is false'; }

            if (!is_numeric($this->Stats['core']['hp'])) { $this->Validated = false; $this->Errors[] = 'hp is false or non numeric'; }
            if (!is_numeric($this->Stats['core']['mp'])) { $this->Validated = false; $this->Errors[] = 'mp is false or non numeric'; }
            if (!is_numeric($this->Stats['core']['tp'])) { $this->Validated = false; $this->Errors[] = 'tp is false or non numeric'; }

            foreach($this->ClassJob['numbered'] as $CJ)
            {
                if (!is_numeric($CJ['level']) && $CJ['level'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
                if (!is_numeric($CJ['exp']['current']) && $CJ['exp']['current'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
                if (!is_numeric($CJ['exp']['max']) && $CJ['exp']['current'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
            }

            $this->log(__LINE__, 'function: validate() - finished');
        }
        public function isValid() { return $this->Validated; }
        public function getErrors() { return $this->Errors; }

        // Data dump
        public function datadump()
        {
            $character_data =
            [
                // Basic profile data
                'id'            => $this->getID(),
                'lsurl'         => $this->getLodestone(),
                'name'          => $this->getName(),
                'server'        => $this->getServer(),
                'title'         => $this->getTitle(),
                'nameclean'     => $this->getNameClean(),
                'avatar'        => $this->getAvatar(),
                'portrait'      => $this->getPortrait('big'),
                'race'          => $this->getRace(),
                'clan'          => $this->getClan(),
                'gender'        => $this->getGender(),
                'legacy'        => $this->getLegacy(),
                'nameday'       => $this->getNameday(),
                'guardian'      => $this->getGuardian(),
                'city'          => $this->getCity(),
                'gender'        => $this->getGender(),

                // Biography
                'biography'     => $this->getBiography(),

                // Company
                'company'       =>
                [
                    'name'      => $this->getCompanyName(),
                    'rank'      => $this->getCompanyRank(),
                ],

                // Free Company
                'freecompany'   => $this->getFreeCompany(),

                // Stats
                'stats'         => $this->getStats(),

                // Gear
                'gear'          => $this->getGear(),

                // Active state
                'active'        =>
                [
                    'class'     => $this->getActiveClass(),
                    'job'       => $this->getActiveJob(),
                    'level'     => $this->getActiveLevel(),
                ],

                // Item level
                'itemlevel'     =>
                [
                    'array'     => $this->getItemLevelArray(),
                    'total'     => $this->getItemLevelTotal(),
                    'average'   => $this->getItemLevelAverage(),
                ],

                // Minions
                'minions'       => $this->getMinions(),

                // Mounts
                'mounts'        => $this->getMounts(),

                // Class jobs
                'classjobs'     => $this->getClassJobs(),
            ];

            return $character_data;
        }

        public function probableJobs()
        {
            $jobarray=$this->getClassJobs("named");
            $req=array('PLD'=>array('main'=>"gladiator", 'sub'=>"conjurer"), 'MNK'=>array('main'=>"pugilist", 'sub'=>"lancer"), 'WAR'=>array('main'=>"marauder", 'sub'=>"gladiator"), 'DRG'=>array('main'=>"lancer", 'sub'=>"marauder"), 'BRD'=>array('main'=>"archer", 'sub'=>"pugilist"), 'WHM'=>array('main'=>"conjurer", 'sub'=>"arcanist"), 'BLM'=>array('main'=>"thaumaturge", 'sub'=>"archer"), 'SMN'=>array('main'=>"arcanist", 'sub'=>"thaumaturge"), 'SCH'=>array('main'=>"arcanist", 'sub'=>"conjurer"));
            $jobs=array("PLD", "MNK", "WAR", "DRG", "BRD", "WHM", "BLM", "SMN", "SCH");
            foreach ($jobs as $job_name)
            {
                if ($jobarray[$req[$job_name]['main']]['level']!="-"&&$jobarray[$req[$job_name]['sub']]['level']!="-"&&$jobarray[$req[$job_name]['main']]['level']>=30&&$jobarray[$req[$job_name]['sub']]['level']>=15)
                {
                    $returnarray[$job_name]=$jobarray[$req[$job_name]['main']]['level'];
                }
            }
            return $returnarray;
        }
    }

    /*  Free Company
     *  ------------
     */
    class FreeCompany extends Parser
    {
        use Funky;
        use Config;

        private $ID;
        private $Lodestone;
        private $Company;
        private $Name;
        private $Server;
        private $Emblum;

        private $Tag;
        private $Formed;
        private $MemberCount;
        private $Slogan;
        private $Ranking;
        private $Focus;
        private $Active;
        private $Seeking;
        private $Recruitment;
        private $Estate;


        private $Members = [];

        #-------------------------------------------#
        # FUNCTIONS                                 #
        #-------------------------------------------#

        // ID
        public function setID($ID, $URL = NULL)
        {
            $this->ID = $ID;
            $this->Lodestone = $URL;
        }
        public function getID() { return $this->ID; }
        public function getLodestone() { return $this->Lodestone; }


        // NAME + SERVER
        public function setBasicData($String)
        {
            $this->Company  = trim($this->strip_html($String[0]));
            $this->Name     = trim($this->strip_html($String[1]));
            $this->Server   = trim(str_ireplace(['(', ')'], null, $this->strip_html($String[2])));
        }

        // Emblum
        public function setEmblum($String)
        {
            $this->Emblum =
            [
                $this->getAttribute('src', $String[2]),
                $this->getAttribute('src', $String[3]),
                $this->getAttribute('src', $String[4]),
            ];
        }
        public function getEmblum() { return $this->Emblum; }

        // TAG + FORMED + MEMBERS + SLOGAN
        public function setFullDetails($String)
        {
            $Temp = [];
            $addToFocus = false;
            $addToSeeking = false;

            foreach($String as $i => $s)
            {
                // Tag
                if (strpos($s, 'table_style2'))
                {
                    $offset = $i + 3;
                    $Temp['tag'] = explode(';', $String[$offset])[14];
                    $Temp['tag'] = trim(str_ireplace('&amp', null, $Temp['tag']));
                    continue;
                }

                // Formed
                if (strpos($s, 'ldst_strftime'))
                {
                    $Temp['formed'] = explode('(', $s)[2];
                    $Temp['formed'] = trim(explode(',', $Temp['formed'])[0]);
                    continue;
                }

                // Active members
                if (strpos($s, 'Active Members'))
                {
                    $offset = $i + 1;
                    $Temp['members'] = $this->strip_html($String[$offset]);
                    continue;
                }

                // Rank / Ranking
                if (strpos($s, 'class') && strpos($s, 'rank'))
                {
                    $offset = $i + 3;
                    $Temp['ranking']['current'] = $this->strip_html($String[$offset]);

                    $offset = $i + 9;
                    $Temp['ranking']['weekly'] = trim(filter_var($this->strip_html($String[$offset]), FILTER_SANITIZE_NUMBER_INT));

                    $offset = $i + 11;
                    $Temp['ranking']['monthly'] = trim(filter_var($this->strip_html($String[$offset]), FILTER_SANITIZE_NUMBER_INT));
                    continue;
                }

                // Slogan
                if (strpos($s, 'Company Slogan'))
                {
                    $offset = $i + 1;
                    $Temp['slogan'] = $this->strip_html($String[$offset]);
                    continue;
                }

                // Focus
                if (strpos($s, 'focus_icon') || $addToFocus)
                {
                    $addToFocus = true;
                    
                    $onOrOff = stripos($s, 'icon_off') !== false ? 0 : 1;

		    $data =
                    [
                        'icon' => trim($this->strip_html($this->getAttribute('src', $s))),
                        'name' => trim($this->strip_html($this->getAttribute('title', $s))),
                        'status' => $onOrOff
                    ];

                    if (isset($data['name']) && strlen($data['name']) > 5)
                    {
                        $data['name'] = str_ireplace('>', null, $data['name']);
                        $Temp['focus'][] = $data;
                    }


                    if (strlen($s) < 20)
                    {
                        $addToFocus = false;
                        continue;
                    }
                }

// Seeking
                if (strpos($s, 'roles_icon') || $addToSeeking)
                {
                    $addToSeeking = true;

                    $onOrOff = stripos($s, 'icon_off') !== false ? 0 : 1;

                    if ($onOrOff == 0) continue;

                    $data =
                    [
                        'icon' => trim($this->strip_html($this->getAttribute('src', $s))),
                        'name' => trim($this->strip_html($this->getAttribute('title', $s)))
                    ];

                    if (isset($data['name']) && strlen($data['name']) > 5)
                    {
                        $data['icon'] = explode('?', $data['icon'])[0];
                        $data['name'] = str_ireplace('>', null, $data['name']);
                        $Temp['seeking'][] = $data;
                    }


                    if (strlen($s) < 20)
                    {
                        $addToSeeking = false;
                        continue;
                    }
                }

                // Active
                if (strpos($s, 'Active') || $addToSeeking)
                {
                    $offset = $i + 2;
                    $Temp['active'] = $this->strip_html($String[$offset]);
                    continue;
                }

                // Recruitment
                if (strpos($s, 'Recruitment') || $addToSeeking)
                {
                    $offset = $i + 2;
                    $Temp['recruitment'] = $this->strip_html($String[$offset]);
                    continue;
                }

                // Estate Profil
                if (strpos($s, 'txt_yellow mb10') || $addToSeeking)
                {
                    $offset = $i;
                    $Temp['estate']['zone'] = $this->strip_html($String[$offset]);

                    $offset = $i + 2;
                    $Temp['estate']['address'] = $this->strip_html($String[$offset]);

                    $offset = $i + 4;
                    $Temp['estate']['message'] = $this->strip_html($String[$offset]);

                    continue;
                }
            }

            $this->Tag          = $Temp['tag'];
            $this->Formed       = $Temp['formed'];
            $this->MemberCount  = $Temp['members'];
            $this->Slogan       = $Temp['slogan'];
            $this->Ranking      = $Temp['ranking'];
            $this->Focus        = isset($Temp['focus']) ? $Temp['focus'] : 'Not specified';
            $this->Active       = $Temp['active'];
            $this->Seeking      = isset($Temp['seeking']) ? $Temp['seeking'] : 'Not specified';
            $this->Recruitment  = $Temp['recruitment'];
            $this->Estate       = isset($Temp['estate']) ? $Temp['estate'] : 'No Estate or Plot';
        }
        public function getCompany() { return $this->Company; }
        public function getName() { return $this->Name; }
        public function getServer() { return $this->Server; }
        public function getTag() { return $this->Tag; }
        public function getFormed() { return $this->Formed; }
        public function getMemberCount() { return $this->MemberCount; }
        public function getSlogan() { return $this->Slogan; }

        public function getRanking() { return $this->Ranking; }
        public function getFocus() { return $this->Focus; }
        public function getActive() { return $this->Active; }
        public function getSeeking() { return $this->Seeking; }
        public function getRecruitment() { return $this->Recruitment; }
        public function getEstate() { return $this->Estate; }


        // MEMBERS / PARSE + SET + GET
        public function parseMembers($Data)
        {
            // Temp array
            $temp = [];

            // Loop through data
            if ($Data)
            {
                foreach($Data as $D)
                {
                    $arr = [];
                    foreach($D as $i => $line)
                    {
                        if (stripos($line, 'thumb_cont_black_50') !== false)
                        {
                            $offset = $i + 2;
                            $arr['avatar'] = $this->getAttribute('src', $D[$offset]);
                            $arr['avatar'] = str_ireplace('50x50', '96x96', $arr['avatar']);
                        }

                        if (stripos($line, 'name_box') !== false)
                        {
                            $offset = $i + 1;
                            $data = $this->strip_html($D[$offset]);
                            $data = explode('(', $data);

                            $arr['name'] = trim($data[0]);
                            $arr['server'] = trim(str_ireplace(')', null, $data[1]));

                            $data = explode('/', $D[$offset]);
                            $arr['id'] = trim($data[3]);
                        }

                        if (stripos($line, 'fc_member_status') !== false)
                        {
                            $offset = $i + 1;
                            $arr['rank']['title'] = trim($this->strip_html($D[$offset]));
                            $arr['rank']['image'] = $this->getAttribute('src', $D[$offset]);
                        }

                        if (stripos($line, 'ic_class') !== false)
                        {
                            $offset = $i + 2;
                            $arr['class']['image'] = $this->getAttribute('src', $D[$offset]);
                        }

                        if (stripos($line, 'lv_class') !== false)
                        {
                            $arr['class']['level'] = filter_var($this->strip_html($line), FILTER_SANITIZE_NUMBER_INT);
                        }

                        if (stripos($line, 'ic_gc') !== false)
                        {
                            $offset = $i + 1;
                            $arr['grandcompany']['image'] = $this->getAttribute('src', $D[$offset]);

                            $data = explode('/', $this->strip_html($D[$offset]));
                            if (isset($data[0]) && !empty($data[0]))
                            {
                                $arr['grandcompany']['name'] = trim($data[0]);
                                $arr['grandcompany']['rank'] = trim($data[1]);
                            }
                            else
                            {
                                // Not in a Grand CompanyIcon
                                $arr['grandcompany']['image'] = null;
                                $arr['grandcompany']['name'] = null;
                                $arr['grandcompany']['rank'] = null;
                            }
                        }
                    }

                    // Append to array
                    $temp[] = $arr;
                }
            }

            // Return temp
            return $temp;
        }
        public function setMembers($Array)
        {
            if (isset($Array) && is_array($Array) && count($Array) > 0)
            {
                $this->Members = $Array;
            }
            else
            {
                $this->Members = false;
            }
        }
        public function getMembers() { return $this->Members; }
    }


    /*  Linkshell
     *  ---------
     */
    class Linkshell extends Parser
    {
        use Funky;
        use Config;

        private $ID;
        private $Name;
        private $Server;
        private $TotalMembers;

        private $Members = [];

        #-------------------------------------------#
        # FUNCTIONS                                 #
        #-------------------------------------------#

        // ID
        public function setID($ID, $URL = NULL)
        {
            $this->ID = $ID;
            $this->Lodestone = $URL;
        }
        public function getID() { return $this->ID; }
        public function getLodestone() { return $this->Lodestone; }

        // NAME + SERVER
        public function setNameServer($String)
        {
            $this->Name     = trim(explode("(", $String[0])[0]);
            $this->Server   = trim(str_ireplace(")", null, explode("(", $String[0])[1]));
        }
        public function getName() { return $this->Name; }
        public function getServer() { return $this->Server; }

        // MEMBER COUNT
        public function setMemberCount($String)
        {
            $this->TotalMembers = intval(trim(preg_replace("/[^0-9]/", "", $String)[0]));
        }
        public function getTotalMembers() { return $this->TotalMembers; }

        // MEMBERS
        public function setMembers($Array)
        {
            $Members = [];

            // Loop through members
            foreach($Array as $i => $arr)
            {
                $temp = [];
                $temp['rank'] = 'member';

                foreach($arr as $ii => $line)
                {
                    if (stripos($line, 'thumb_cont_black_50') !== false)
                    {
                        $offset = $ii + 1;
                        $temp['id'] = explode('/', $arr[$offset]);
                        $temp['id'] = $temp['id'][3];

                        $offset = $ii + 2;

                        $temp['avatar'] = $arr[$offset];
                        $temp['avatar'] = $this->getAttribute('src', $temp['avatar']);
                    }

                    if (stripos($line, 'name_box') !== false)
                    {
                        $offset = $ii + 1;
                        $temp['name'] = $arr[$offset];
                        $temp['name'] = $this->strip_html($temp['name']);
                        $temp['name'] = explode('(', $temp['name']);
                        $temp['server'] = trim(str_ireplace(')', null, $temp['name'][1]));
                        $temp['name'] = trim($temp['name'][0]);
                    }

                    if (stripos($line, 'ic_master') !== false)
                    {
                        $temp['rank'] = 'master';
                    }

                    if (stripos($line, 'ic_leader') !== false)
                    {
                        $temp['rank'] = 'leader';
                    }

                    if (stripos($line, 'ic_class') !== false)
                    {
                        $offset = $ii + 1;
                        $temp['class']['icon'] = $this->getAttribute('src', $arr[$offset]);

                        $offset = $ii + 2;
                        $temp['class']['level'] = filter_var($arr[$offset], FILTER_SANITIZE_NUMBER_INT);;

                    }

                    if (stripos($line, 'col3box_center') !== false)
                    {
                        $offset = $ii + 1;
                        $temp['grandcompany']['icon'] = $this->getAttribute('src', $arr[$offset]);

                        $data = explode('/', $this->strip_html($arr[$offset]));
                        $temp['grandcompany']['name'] = $data[0];
                        $temp['grandcompany']['rank'] = $data[1];
                    }

                    if (stripos($line, 'col3box_center') !== false)
                    {
                        $offset = $ii + 1;
                        $temp['grandcompany']['icon'] = $this->getAttribute('src', $arr[$offset]);

                        $data = explode('/', $this->strip_html($arr[$offset]));
                        $temp['grandcompany']['name'] = $data[0];
                        $temp['grandcompany']['rank'] = $data[1];
                    }

                    if (stripos($line, 'ic_crest_32') !== false)
                    {
                        $offset = $ii + 2;
                        $temp['company']['icon'][] = $this->getAttribute('src', $arr[$offset]);

                        $offset = $ii + 3;
                        $temp['company']['icon'][] = $this->getAttribute('src', $arr[$offset]);

                        $offset = $ii + 4;
                        $temp['company']['icon'][] = $this->getAttribute('src', $arr[$offset]);
                    }

                    if (stripos($line, 'txt_gc') !== false)
                    {
                        $temp['company']['name'] = $this->strip_html($line);
                        $temp['company']['id'] = explode('/', $line)[4];
                    }
                }

                // append to temp array
                $Members[] = $temp;
            }

            // Set Members
            $this->Members = $Members;
        }
        public function getMembers() { return $this->Members; }
    }


    /*  Achievement
     *  -----------
     */
    class Achievements extends Parser
    {
        use Funky;
        use Config;

        private $TotalPoints = 0;
        private $CurrentPoints = 0;
        private $PointsPercentage = 0;
        private $TotalAchievements = 0;
        private $CurrentAchievements = 0;
        private $Categories = [];
        private $List = [];

        // POINTS
        public function setTotalPoints($Value) { $this->TotalPoints = $Value; }
        public function setCurrentPoints($Value) { $this->CurrentPoints = $Value; }
        public function setPointsPercentage($Value) { $this->PointsPercentage = $Value; }
        public function setTotalAchievements($Value) { $this->TotalAchievements = $Value; }
        public function setCurrentAchievements($Value) { $this->CurrentAchievements = $Value; }

        public function getTotalPoints() { return $this->TotalPoints; }
        public function getCurrentPoints() { return $this->CurrentPoints; }
        public function getPointsPercentage() { return $this->PointsPercentage; }
        public function getTotalAchievements() { return $this->TotalAchievements; }
        public function getCurrentAchievements() { return $this->CurrentAchievements; }

        // CATEGORY
        public function addCategory($ID) { $this->Categories[] = $ID; }
        public function setCategories($List) { $this->Categories = $List; }
        public function getCategories() { return $this->Categories; }

        // ACHIEVEMENTS
        public function set($Array)
        {
            // New list of achievements
            $NewList = array();

            // Loop through achievement blocks
            if ($Array)
            {
                foreach($Array as $A)
                {
                    // Temp data array
                    $Temp = array();

                    // Loop through block data
                    $i = 0;
                    foreach($A as $Line)
                    {
                        // Get achievement Data
                        if (stripos($Line, 'achievement_name') !== false)
                        {
                            $Data = trim(strip_tags(html_entity_decode($Line)));
                            $Temp['name'] = str_ireplace("&#39;", "'", $Data);
                        }
                        if (stripos($Line, 'achievement_point') !== false)
                        {
                            $Data = trim(strip_tags(html_entity_decode($Line)));
                            $Temp['points'] = intval(htmlspecialchars_decode($Data));
                        }
                        if (stripos($Line, 'getElementById') !== false)
                        {
                            $Temp['date'] = trim(filter_var(explode("(", strip_tags(html_entity_decode($Line)))[2], FILTER_SANITIZE_NUMBER_INT));
                        }
                        if (stripos($Line, 'bt_more') !== false)
                        {
                            $Temp['id'] = explode("/", $Line)[6];
                            $Temp['xivdb'] = 'http://xivdb.com/?achievement/'. $Temp['id'] .'/'. str_ireplace(' ', '-', $Temp['name']);
                        }

                        // Increment
                        $i++;
                    }

                    // Obtained or not, if there is a date, the achievement is obtained.
                    if (isset($Temp['date'])) { $Temp['obtained'] = true; } else { $Temp['obtained'] = false; }

                    // If achievement obtained, add points
                    if ($Temp['obtained'])
                    {
                        $this->CurrentPoints += $Temp['points'];
                        $this->CurrentAchievements++;
                    }

                    // Set the total obtainable points
                    $this->TotalPoints += $Temp['points'];
                    $this->TotalAchievements++;

                    // Append temp data
                    $NewList[$Temp['id']] = $Temp;
                }
            }

            // Set points percentage
            if ($this->CurrentPoints > 0 && $this->TotalPoints > 0)
            {
                $this->PointsPercentage = (round($this->CurrentPoints / $this->TotalPoints, 3) * 100);
            }


            // Set Achievement List
            $this->List = $NewList;
        }
        public function get() { return $this->List; }
        public function addAchievements($List) { $this->List = array_merge($this->List, $List); }
        public function genPointsPercentage() { $this->PointsPercentage = (round($this->CurrentPoints / $this->TotalPoints, 3) * 100); }

        public function checkIfPublic($Array)
        {
            /*
                This is so dirty... but it'll do
            */
            if (isset($Array[0]))
            {
                if (stripos($Array[0][0], 'do not have permission') !== false)
                {
                    return false;
                }
            }

            return true;
        }

        public function setSummary($Array)
        {
            // placeholder
            $achievements = [];

            // loop
            foreach($Array as $arr)
            {
                $temp = [];

                foreach($arr as $i => $line)
                {
                    if (stripos($line, 'ic_achievement') !== false)
                    {
                        $offset = $i + 1;
                        $temp['id'] = explode("/", $arr[$offset])[6];
                    }

                    if (stripos($line, 'lds/pc/global') !== false)
                    {
                        $temp['icon'] = $this->getAttribute('src', $line);
                    }

                    if (stripos($line, 'ldst_strftime') !== false)
                    {
                        $temp['time'] = explode('(', $line)[2];
                        $temp['time'] = explode(',', $temp['time'])[0];
                    }

                    if (stripos($line, '/script') !== false)
                    {
                        $offset = $i + 2;
                        $temp['title'] = $this->strip_html($arr[$offset]);
                    }
                }

                $achievements[$temp['id']] = $temp;
            }

            $this->List = $achievements;
        }
    }


    /*  Blog
     *  -----------
     */
    class Blog extends Parser
    {
        use Funky;
        use Config;
		
        private $Entries = [];
        private $cID = null;
        private $blogUrlBase = null;
		
		public function __construct($cID=null){
			if(is_null($cID) === false){
				$this->setCharacterID($cID)
					 ->setBlogURL($this->URL['character']['profile'] . $this->cID . $this->URL['character']['blog']);
			}
			return $this;
		}
		
		public function setCharacterID($cID){ $this->cID = $cID; return $this; }
		public function getCharacterID(){ return $this->cID; }
		
		public function setBlogURL($url){ $this->blogUrlBase = $url; return $this; }
		public function getBlogURL(){ return $this->blogUrlBase; }
		
		public function setEntries($withFullText=false){
			// Get Data from URL
			\phpQuery::newDocumentFileHTML($this->blogUrlBase);
			// Loop it
			foreach(pq('#mydiary_list>div.base_header_long') as $node){
                $temp = [];
				$pqNode = pq($node);
				// Headline & ID
				$headlineNode = $pqNode->find('h1>a.large:first');
				$temp['url'] = $headlineNode->attr('href');
				$temp['id'] = explode("/", $temp['url'])[5];
				$temp['headline'] = $headlineNode->text();
				//Tags
				$temp['tags'] = $this->_getTags(pq('div.diary_tag_body_long',$node));
				//Body
				$temp['shorttext'] = trim($pqNode->find('div.comment')->text());
				if($withFullText === true){
					$blogEntryDom = \phpQuery::newDocumentFileHTML($this->blogUrlBase .$temp['id'] )->find('div.diary_flame');
					$temp['fulltext'] = $this->_getFulltext($blogEntryDom);
				}
				
				$this->Entries[$temp['id']] = $temp;
			}
			return $this;
		}
		
		public function getEntryByID($bID){
			if (!$bID){
                echo "No blog id set.";
            }else{
				if(!isset($this->Entries[$bID]) || !array_key_exists('fulltext',$this->Entries[$bID])){
					$this->Entries[$bID]['url'] = $this->blogUrlBase . $bID;
					$blogEntryDom = \phpQuery::newDocumentFileHTML($this->Entries[$bID]['url'])->find('div.diary_flame');
					$this->Entries[$bID]['id'] = $bID;
					$this->Entries[$bID]['headline'] = trim(pq('div.area_footer>h1:first',$blogEntryDom)->text());
					$this->Entries[$bID]['tags'] = $this->_getTags(pq('div.diary_tag_body_w570',$blogEntryDom));
					$this->Entries[$bID]['fulltext'] = $this->_getFulltext($blogEntryDom);
					// Shorttext is not really needed
					//$this->Entries[$bID]['shorttext'] = trim(pq('div.area_footer>h1:first',$blogEntryDom)->text());
				}
				return $this->Entries[$bID];
			}
		}
		
		private function _getFulltext($domNode){
			return trim(pq('div.body:first',$domNode)->html());
		}
		
		private function _getTags($domNode){
			$tags = [];
			foreach(pq('a',$domNode) as $tagLinkNode){
				$linkNode =  pq($tagLinkNode);
				$tagName = trim(str_replace(array('[',']'),'',$linkNode->text()));
				$tagLink = $linkNode->attr('href');
				$tags[$tagName] = array(
					'name' => $tagName,
					'url' => $tagLink
				);
			}
			return $tags;
		}
    }

    /*  Parser
     *  ------
     *  > getSource - $URL [protected] (Fetches the source code of the specified url.)
     *  > curl - $URL [private] (Core curl function with additional options.)
     */
    class Parser
    {
        // The source code of the most recent curl
        protected $SourceCodeArray;

        // Find data based on a tag
        protected function find($Tag, $Clean = TRUE)
        {
           $this->log(__LINE__, '[parser] find()', [$Tag, $Clean]);

            // Search for element
            foreach($this->SourceCodeArray as $Line)
            {
                // Trim line
                $Line = trim($Line);

                // Search line
                if(stripos($Line, $Tag) !== false)
                {
                    // If clean, clean it!
                    if ($Clean) { $Line = $this->Clean(strip_tags(html_entity_decode($Line))); }

                    // If empty, return true for "found", else return line.
                    if (empty($Line))
                        return true;
                    else
                        return $Line;
                }
            }

            // No find
            return false;
        }

        // Find data based on a tag, and take the next i amount
        protected function findRange($Tag, $Range, $Tag2 = NULL, $Clean = TRUE, $StartAt = 1)
        {
            $this->log(__LINE__, '[parser] findRange()', [$Tag, $Range, $Tag2, $Clean, $StartAt]);

            $Found      = false;
            $Found2     = false;
            $Interates  = 0;
            $Array      = NULL;

            // If range null
            if (!$Range) { $Range = 9999; }

            // Search for element
            foreach($this->SourceCodeArray as $Line)
            {
                // Trim line
                $Line = trim($Line);

                // Search line, mark found
                if(stripos($Line, $Tag) !== false) { $Found = true; }
                if(stripos($Line, $Tag2) !== false) { $Found2 = true; }

                if ($Found)
                {
                    // Iterate
                    $Interates++;

                    // Check if we reached the StartAt value
                    if($Interates < $StartAt)
                    {
                        $Found = false;
                        $Found2 = false;
                        continue;
                    }

                    // If clean true, clean line!
                    if ($Clean) { $Array[] = $this->Clean(strip_tags(html_entity_decode($Line))); } else { $Array[] = $Line; }

                    // If iterate hits range, break.
                    if ($Interates == $Range  || $Found2) { break; }
                }
            }

            // Remove empty values
            $Array = isset($Array) ? array_values(array_filter($Array)) : NULL;

            // Return array, else false.
            if ($Array)
                return $Array;
            else
                return false;
        }

        // Finds all entries based on a tag, and take the next i amount
        protected function findAll($Tag, $Range, $Tag2 = NULL, $Clean = TRUE)
        {
            $this->log(__LINE__, '[parser] findAll()', [$Tag, $Range, $Tag2, $Clean]);

            $Found      = false;
            $Found2     = false;
            $Interates  = 0;
            $Array      = NULL;
            $Array2     = NULL;

            // If range null
            if (!$Range) { $Range = 9999; }

            // Search for element
            foreach($this->SourceCodeArray as $Line)
            {
                // Trim line
                $Line = trim($Line);

                // Search line, mark found
                if(stripos($Line, $Tag) !== false && $Tag) { $Found = true; }
                if(stripos($Line, $Tag2) !== false && $Tag2 && $Found) { $Found2 = true; }

                if ($Found)
                {
                    // If clean true, clean line!
                    if ($Clean) { $Array[] = $this->Clean(strip_tags(html_entity_decode($Line))); } else { $Array[] = $Line; }

                    // Iterate
                    $Interates++;

                    // If iterate hits range, append to array and null.
                    if ($Interates == $Range || $Found2)
                    {
                        // Remove empty values
                        $Array = array_values(array_filter($Array));

                        // Append
                        $Array2[] = $Array;
                        $Array = NULL;

                        // Reset founds
                        $Found      = false;
                        $Found2     = false;
                        $Interates  = 0;
                    }
                }
            }

            // Return array, else false.
            if ($Array2)
                return $Array2;
            else
                return false;
        }

        // Removes section of array up to specified tag
        protected function segment($Tag)
        {
            $this->log(__LINE__, '[parser] segment()', [$Tag]);

            // Loop through source code array
            $i = 0;
            foreach($this->SourceCodeArray as $Line)
            {
                // If find tag, break
                if(stripos($Line, $Tag) !== false) { break; }
                $i++;
            }

            // Splice array
            array_splice($this->SourceCodeArray, 0, $i);
        }

        // Clean a found results
        protected function clean($Line)
        {
            $this->log(__LINE__, '[parser] clean()');

            // Strip tags
            $Line = strip_tags(html_entity_decode($Line));

            // Random removals
            $Remove = array("-->");
            $Line = str_ireplace($Remove, NULL, $Line);

            // Return value
            return $Line;
        }

        // Strip html
        protected function strip_html($Line)
        {
            $this->log(__LINE__, '[parser] strip_html()');
            return trim(strip_tags(htmlspecialchars_decode(trim($Line), ENT_QUOTES)));
        }

        // Get attribute
        protected function getAttribute($attribute, $string)
        {
            $this->log(__LINE__, '[parser] getAttribute() = '. $attribute);

            // Add =
            $attribute = $attribute .'=';

            // Explode string by each param
            $string = explode(' ', $string);

            // Loop through to find 'attribute',
            foreach($string as $s)
            {
                if (stripos($s, $attribute) !== false)
                {
                    $string = $s;
                    break;
                }
            }

            // Strip stuff
            $string = str_ireplace([$attribute, '&quot;'], null, $string);

            if (is_array($string))
            {
                $string = implode(null, $string);
            }


            // return
            return $string;
        }

        // Get the DOMDocument from the source via its URL.
        protected function getSource($URL)
        {
            $this->log(__LINE__, '[parser] getSource() - URL = '. $URL);

            // Get the source of the url
            # Show($URL);
            $Source = $this->curl($URL);
            $this->SourceCodeArray = explode("\n", $Source);

            // Return
            $this->log(__LINE__, '[parser] getSource() - COMPLETE : URL = '. $URL);
            return true;
        }

        // Fetches page source via CURL
        protected function curl($URL)
        {
            $this->log(__LINE__, '[parser] curl() - URL = '. $URL);

            $options = array(
                CURLOPT_RETURNTRANSFER  => true,            // return web page
                CURLOPT_HEADER          => false,           // return headers
                CURLOPT_FOLLOWLOCATION  => false,           // follow redirects
                CURLOPT_ENCODING        => "",              // handle all encodings
                CURLOPT_AUTOREFERER     => true,            // set referer on redirect
                CURLOPT_CONNECTTIMEOUT  => 15,              // timeout on connects
                CURLOPT_TIMEOUT         => 15,              // timeout on response
                CURLOPT_MAXREDIRS       => 5,               // stop after 10 redirects
                CURLOPT_USERAGENT       => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36",
                CURLOPT_HTTPHEADER      => array('Content-type: text/html; charset=utf-8', 'Accept-Language: en'),
            );

            $ch = curl_init($URL);
            curl_setopt_array($ch, $options);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/html; charset=utf-8'));
            $source = curl_exec($ch);
            curl_close($ch);


            $html = htmlentities($source);
            //Show($html);

            $this->log(__LINE__, '[parser] curl() - COMPLETE : URL = '. $URL);
            return $html;
        }

        // Prints the source array
        public function printSourceArray()
        {
            $this->show($this->SourceCodeArray);
        }
    }
?>
