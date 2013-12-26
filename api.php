<?php
	/*
		XIVPads.com (v4) - Lodestone Query API
		--------------------------------------------------
		Author: 	Josh Freeman (Premium Virtue)
		Support:	http://xivpads.com/?Portal
		Version:	4.0
		PHP:		5.4
		
		Always ensure you download from the github
		https://github.com/viion/XIVPads-LodestoneAPI
		--------------------------------------------------
	*/

	// Debug stuff
 	//error_reporting(-1);
	//if (!function_exists('show')) { function show($Data) { echo '<pre>'; print_r($Data); echo '</pre>'; } }

	/*	LodestoneAPI
	 *	------------
	 */
	class LodestoneAPI extends Parser
	{
		// url addresses to various lodestone content. (DO NOT CHANGE, it will break some functionality of the API)
		private $URL =
		[
			# Search related urls
			'search' =>
			[
				'query'			=> '?q=%name%&worldname=%server%',
			],

			# Character related urls
			'character' => 
			[	
				'profile'		=> 'http://eu.finalfantasyxiv.com/lodestone/character/',
				'achievement' 	=> '/achievement/kind/',
			],

			# Free company related urls
			'freecompany' => 
			[
				'profile'		=> 'http://eu.finalfantasyxiv.com/lodestone/freecompany/',
				'member' 		=> '/member/',
				'memberpage' 	=> '?page=%page%',
			],

			# Linkshell related urls
			'linkshell' =>
			[
				'profile'		=> 'http://eu.finalfantasyxiv.com/lodestone/linkshell/',
				'activity'		=> '/activity/',
			],

			# Topics
			'lodestone' =>
			[
				'topic' 		=> 'http://eu.finalfantasyxiv.com/lodestone/topics/',
			],
		];

		// defaults
		private $defaults =
		[
			'automaticallyParseFreeCompanyMembers' => false,
			'pagesPerFreeCompanyMemberList' => 20,
		];
		
		// Configuration
		public $AchievementCategories = [1, 2, 4, 5, 6, 8, 11, 12, 13];
		public $ClassList = [];
		public $ClassDisicpline = [];
		public $GearSlots = 
		[
			"main","main2","shield","soul crystal",
			"head","body","hands","waist","legs","feet",
			"necklace","earrings","bracelets","ring","ring2"
		];

		
		// List of characters parsed
		public $Characters = [];
		public $Achievements = [];
		public $Search = [];
		
		// List of free company data parsed
		public $FreeCompanyList = [];
		public $FreeCompanyMembersList = [];

		// List of linkshell data parsed
		public $Linkshells = [];
		
		public function __construct()
		{
			// Set classes
			$this->ClassList = array(
				"Gladiator", "Pugilist", "Marauder", "Lancer", "Archer", "Conjurer", "Thaumaturge", "Arcanist", "Carpenter", "Blacksmith", 
				"Armorer", "Goldsmith", "Leatherworker", "Weaver", "Alchemist", "Culinarian", "Miner", "Botanist", "Fisher"
			);
			
			// Set class by disicpline							
			$this->ClassDisicpline = array(
				"dow" => array_slice($this->ClassList, 0, 5),
				"dom" => array_slice($this->ClassList, 5, 3),
				"doh" => array_slice($this->ClassList, 8, 8),
				"dol" => array_slice($this->ClassList, 16, 3),
			);
		}
		
		// Quick get
		public function get($Array, $Options = null)
		{
			// Clean
			$Name 	= isset($Array['name']) 	? trim(ucwords($Array['name'])) : NULL;
			$Server = isset($Array['server']) 	? trim(ucwords($Array['server'])) : NULL;
			$ID		= isset($Array['id']) 		? trim($Array['id']) : NULL;
			
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
				return $this->getCharacterByID($ID);
			}
			else
			{
				return false;
			}
		}

		// Quick get free company
		public function getFC($Array, $Options = null)
		{
			// Clean
			$Name 	= isset($Array['name']) 	? trim(ucwords($Array['name'])) : NULL;
			$Server = isset($Array['server']) 	? trim(ucwords($Array['server'])) : NULL;
			$ID		= isset($Array['id']) 		? trim($Array['id']) : NULL;

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

		// Quick get linkshell
		public function getLS($Array, $Options = null)
		{
			// Clean
			$Name 	= isset($Array['name']) 	? trim(ucwords($Array['name'])) : NULL;
			$Server = isset($Array['server']) 	? trim(ucwords($Array['server'])) : NULL;
			$ID		= isset($Array['id']) 		? trim($Array['id']) : NULL;

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
		public function Lodestone($Options)
		{
			// Get a Lodestone object
			$Lodestone = new Lodestone();

			// Lodestone urls
			$Lodestone->setURLs($this->URL['lodestone']);

			// If topics option set, get topics
			if (isset($Options['topics']) && $Options['topics'])
			{
				$this->getSource($this->URL['lodestone']['topic']);
				$Lodestone->setTopics($this->findAll('topics_list_inner', NULL, 'right_cont clearfix', false));
			}

			// Return lodestone object.
			return $Lodestone;
		}

		#-------------------------------------------#
		# SEARCH									#
		#-------------------------------------------#

		// Search a character by its name and server.
		public function searchCharacter($Name, $Server, $GetExact = true)
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
				$this->getSource($this->URL['character']['profile'] . str_ireplace(array('%name%', '%server%'), array(str_ireplace(" ", "+", $Name), $Server), $this->URL['search']['query']));

				// Get all found characters
				$Found = $this->findAll('thumb_cont_black_50', 10, NULL, false);

				// Loop through results
				if ($Found)
				{
					foreach($Found as $F)
					{
						$Avatar 	= explode('&quot;', $F[1])[3];
						$Data 		= explode('&quot;', $F[6]);
						$ID			= trim(explode('/', $Data[3])[3]);
						$NameServer	= explode("(", trim(str_ireplace(">", NULL, strip_tags(html_entity_decode($Data[4]))))); 
						$Name		= htmlspecialchars_decode(trim($NameServer[0]), ENT_QUOTES);
						$Server		= trim(str_ireplace(")", NULL, $NameServer[1]));
						$Language 	= $F[4];
						
						// Append search results
						$this->Search['results'][] = array(
							"avatar" 	=> $Avatar,
							"name"		=> $Name,
							"server"	=> $Server,
							"id"		=> $ID,
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
							if (($Character['name']) == ($ExactName) && strlen(trim($Character['name'])) == strlen(trim($ExactName)))
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
				$Found = $this->findAll('groundcompany_name', 20, NULL, false);
				
				// if found
				if ($Found)
				{
					foreach($Found as $F)
					{
						$Company 	= $this->clean($F[0]);
						$ID			= trim(explode("/", $F[2])[3]);
						$Name 		= trim(explode("(", $this->clean($F[2]))[0]);
						$Server 	= trim(str_ireplace(")", "", explode("(", $this->clean($F[2]))[1]));
						$Members 	= trim(explode(":", $this->clean($F[5]))[1]);
						$Formed 	= trim(explode(",", explode("(", $F[10])[2])[0]);

						$this->Search['results'][] = 
						array(
							"id"		=> $ID,
							"company" 	=> $Company,
							"name"		=> $Name,
							"server"	=> $Server,
							"members"	=> $Members,
							"formed"	=> $Formed,
						);
					}

					// If to get exact
					if ($GetExact)
					{
						$Exact = false;
						foreach($this->Search['results'] as $FreeCompany)
						{
							if (($FreeCompany['name']) == ($ExactName) && strlen(trim($FreeCompany['name'])) == strlen(trim($ExactName)))
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
						$ID 		= trim(explode("/", $F[0])[3]);
						$Name 		= trim(str_ireplace(['&quot;', '&lt;', '&gt;'], null, explode("/", $F[0])[4]));
						$Server 	= trim(strip_tags(html_entity_decode(str_ireplace(")", null, explode("(", $F[0])[1]))));
						$Members	= trim(explode(":", strip_tags(html_entity_decode($F[3])))[1]);

						$this->Search['results'][] = 
						[
							"id"		=> $ID,
							"name"		=> $Name,
							"server"	=> $Server,
							"members"	=> $Members,
						];
					}

					// If to get exact
					if ($GetExact)
					{
						$Exact = false;
						foreach($this->Search['results'] as $Linkshell)
						{
							if (($Linkshell['name']) == ($ExactName) && strlen(trim($Linkshell['name'])) == strlen(trim($ExactName)))
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
			// Get the source
			$this->getSource($this->URL['character']['profile'] . $ID);

			// Check character tag
			$PageNotFound = $this->find('/lodestone/character/');
			
			// if not found, error.
			if (!$PageNotFound) { return true; }

			return false;
		}
		
		#-------------------------------------------#
		# PROFILE									#
		#-------------------------------------------#
		
		// Parse a profile based on ID
		public function parseProfile($ID)
		{
			if (!$ID)
			{
				echo "error: No ID Set.";	
			}
			else if ($this->errorPage($ID))
			{
				echo "error: Character page does not exist.";	
			}
			else
			{
				// Get the source
				$this->getSource($this->URL['character']['profile'] . $ID);
				
				// Create a new character object
				$Character = new Character();
				
				// Set Character Data
				$Character->setID(trim($ID), $this->URL['character']['profile'] . $ID);
				$Character->setNameServer($this->findRange('player_name_thumb', 15));

				// Only process if character name set
				if (strlen($Character->getName()) > 3)
				{
					$Character->setAvatar($this->findRange('player_name_thumb', 10, NULL, false));
					$Character->setPortrait($this->findRange('bg_chara_264', 2, NULL, false));
					$Character->setRaceClan($this->find('chara_profile_title'));
					$Character->setLegacy($this->find('bt_legacy_history'));
					$Character->setBirthGuardianCompany($this->findRange('chara_profile_list', 60, NULL, false));
					$Character->setCity($this->findRange('City-state', 5));
					$Character->setBiography($this->findRange('txt_selfintroduction', 5));
					$Character->setHPMPTP($this->findRange('param_power_area', 10));
					$Character->setAttributes($this->findRange('param_list_attributes', 8));
					$Character->setElemental($this->findRange('param_list_elemental', 8));
					$Character->setOffense($this->findRange('param_title_offense', 6));
					$Character->setDefense($this->findRange('param_title_deffense', 6));
					$Character->setPhysical($this->findRange('param_title_melle', 6));
					$Character->setResists($this->findRange('param_title_melleresists', 6));
					$Character->setActiveClassLevel($this->findRange('&quot;class_info&quot;', 3));
					
					// Set Gear (Also sets Active Class and Job)
					$Gear = $this->findAll('item_detail_box', NULL, '//ITEM Detail', false);
					$Character->setGear($Gear);
					
					// The next few attributes are based on class
					if (in_array($Character->getActiveClass(), $this->ClassDisicpline['dow']) || in_array($Character->getActiveClass(), $this->ClassDisicpline['dom']))
					{
						$Character->setSpell($this->findRange('param_title_spell', 6));
						$Character->setPVP($this->findRange('param_title_pvpparam', 6));
					}
					else if (in_array($Character->getActiveClass(), $this->ClassDisicpline['doh']))
					{
						$Character->setCrafting($this->findRange('param_title_crafting', 6));
					}
					else if (in_array($Character->getActiveClass(), $this->ClassDisicpline['dol']))
					{
						$Character->setGathering($this->findRange('param_title_gathering', 6));
					}

					#$this->segment('area_header_w358_inner');
					
					// Set Minions
					$Minions = $this->findRange('area_header_w358_inner', NULL, '//Minion', false);
					$Character->setMinions($Minions);
					
					// Set Mounts
					$Mounts = $this->findRange('area_header_w358_inner', NULL, '//Mount', false, 2);
					$Character->setMounts($Mounts);
					
					#$this->segment('class_fighter');
					
					// Set ClassJob
					$Character->setClassJob($this->findRange('class_fighter', NULL, '//Class Contents', false));
					
					// Validate data
					$Character->validate();
					
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
		public function getCharacters() { return $this->Characters;	}
		
		// Get a character by id
		public function getCharacterByID($ID) { return isset($this->Characters[$ID]) ? $this->Characters[$ID] : NULL; }
		
		#-------------------------------------------#
		# ACHIEVEMENTS								#
		#-------------------------------------------#
		
		// Get a list of parsed characters
		public function getAchievements() { return $this->Achievements; }
		
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
				$Achievements->addCategory($cID);
				$Achievements->set($this->findAll('achievement_area_body', NULL, 'bt_more', false));
				
				// Append character to array
				$this->Achievements[$cID] = $Achievements;
			}
		}

		// Get the achievement categories
		public function getAchievementCategories()
		{
			return $this->AchievementCategories;
		}

		#-------------------------------------------#
		# FREE COMPANY								#
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
				$FreeCompany->setNameServerCompany($this->findRange('-- playname --', null, '-- //playname --', false));
				$FreeCompany->setCompanyDetails($this->findRange('-- Company Profile --', null, '-- //Company Profile --', false));

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
						$MemberArray = $FreeCompany->parseMembers($this->findAll('player_name_area', 18, null, null));

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
		# LINKSHELL									#
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
				$Linkshell->setMembers($this->findAll('thumb_cont_black_50', 50, false, false));


				// Save free company
				$this->Linkshells[$ID] = $Linkshell;
			}
		}

		// Get a list of parsed linkshells.
		public function getLinkshells() { return $this->Linkshells; }

		// Get a linkshell by id
		public function getLinkshellByID($ID) { return isset($this->Linkshells[$ID]) ? $this->Linkshells[$ID] : NULL; }

		#-------------------------------------------#
		# FUNCTIONS									#
		#-------------------------------------------#

		// This function will sort a multi dimentional array based on a key index, its global, do not use $var = sksort().
		protected function sksort(&$array, $subkey, $sort_ascending) 
		{
			if (count($array))
				$temp_array[key($array)] = array_shift($array);
			foreach($array as $key => $val){
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
				if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
			}
			if ($sort_ascending)
				$array = array_reverse($temp_array);
			else 
				$array = $temp_array;
		}
	}

	/*	Lodestone
	 *	---------
	 */
	class Lodestone
	{
		// Variables
		private $URLs = [];
		private $Topics = [];


		// Construct
		function __construct() { }

		// set urls
		function setURLs($URLs)
		{
			$this->URLs = $URLs;
		}

		// Topics
		function setTopics($Data)
		{
			// Total topics
			$this->Topics['total'] = count($Data);

			// Loop through topics to get data
			$TopicData = [];
			foreach($Data as $i => $D)
			{
				// Time
				$TopicData[$i]['time'] 	= trim(explode(",", explode("(", $D[3])[2])[0]);
				$TopicData[$i]['url'] 	= trim(str_ireplace("/lodestone/topics/", null, $this->URLs['topic']) . explode('&quot;', explode("&gt;", $D[6])[0])[1]);
				$TopicData[$i]['title'] = trim(explode("(", iconv("UTF-8", "ASCII//TRANSLIT", trim(strip_tags(html_entity_decode($D[6])))))[0]);
				$TopicData[$i]['image'] = trim(explode('&quot;', $D[8])[3]);
			}

			$this->Topics['data'] = $TopicData;
		}
		function getTopics() { return $this->Topics; }
	}


	/*	Character
	 *	---------
	 */
	class Character
	{
		private $ID;
		private $Lodestone;
		private $Name;
		private $NameClean;
		private $Server;
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
		# FUNCTIONS									#
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
			$this->Name 	= trim($String[0]);
			$this->Server 	= trim(str_ireplace(["(", ")"], null, $String[1]));
			$this->NameClean= preg_replace('/[^a-z]/i', '', strtolower($this->Name));	
		}
		public function getName() { return $this->Name; }
		public function getServer() { return $this->Server; }
		public function getNameClean() { return$this->NameClean; }
		
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
		public function getAvatar($Size) { return $this->Avatars[$Size]; }
		
		// PORTRAIT
		public function setPortrait($String)
		{
			if (isset($String))
			{
				$this->Portrait = trim(explode('&quot;', $String[1])[1]);
			}
		}
		public function getPortrait() { return $this->Portrait; }
		
		// RACE + CLAN
		public function setRaceClan($String)
		{
			if (isset($String))
			{
				$String 		= explode("/", $String);
				$this->Clan 	= htmlspecialchars_decode(trim($String[1]), ENT_QUOTES);
				$this->Race 	= htmlspecialchars_decode(trim($String[0]), ENT_QUOTES);
			}
		}
		public function getRace() { return $this->Race; }
		public function getClan() { return $this->Clan; }
		
		// LEGACY
		public function setLegacy($String) { $this->Legacy = $String; }
		public function getLegacy() { return $this->Legacy; }
		
		// BIRTHDATE + GUARDIAN + COMPANY + FREE COMPANY
		public function setBirthGuardianCompany($String)
		{
			$this->Nameday 		= trim(strip_tags(html_entity_decode($String[11])));
			$this->Guardian 	= str_ireplace("&#39;", "'", trim(strip_tags(html_entity_decode($String[15]))));
				
			$i = 0;
			foreach($String as $Line)
			{
				if (stripos($Line, 'Grand Company') !== false) 	{ $Company = trim(strip_tags(html_entity_decode($String[($i + 1)]))); }
				if (stripos($Line, 'Free Company') !== false) 	{ $FreeCompany = trim($String[($i + 1)]); }
				$i++;
			}
			
			// If grand company
			if (isset($Company))
			{
				$this->Company 		= array("name" => explode("/", $Company)[0], "rank" => explode("/", $Company )[1]);
			}
			
			// If free company
			if (isset($FreeCompany))
			{
				$FreeCompanyID		= trim(filter_var(explode('&quot;', $FreeCompany)[1], FILTER_SANITIZE_NUMBER_INT));
				$this->FreeCompany 	= array("name" => trim(strip_tags(html_entity_decode($FreeCompany))), "id" => $FreeCompanyID);
			}
		}
		public function getNameday() 		{ return $this->Nameday; }
		public function getGuardian() 		{ return $this->Guardian; }
		public function getCompanyName() 	{ return $this->Company['name']; }
		public function getCompanyRank() 	{ return $this->Company['rank']; }
		public function getFreeCompany() 	{ return $this->FreeCompany; }
		
		// CITY
		public function setCity($String) { $this->City = htmlspecialchars_decode(trim($String[1]), ENT_QUOTES); }
		public function getCity() { return $this->City; }
		
		// BIOGRAPHY
		public function setBiography($String) { $this->Biography = trim($String[0]); }
		public function getBiography() { return $this->Biography; }
		
		// HP + MP + TP
		public function setHPMPTP($String) 
		{ 
			$this->Stats['core']['hp'] = trim($String[0]);
			$this->Stats['core']['mp'] = trim($String[1]);
			$this->Stats['core']['tp'] = trim($String[2]);
		}
		
		// ATTRIBUTES
		public function setAttributes($String) 
		{ 
			$this->Stats['attributes']['strength'] 		= trim($String[0]);
			$this->Stats['attributes']['dexterity'] 	= trim($String[1]);
			$this->Stats['attributes']['vitality'] 		= trim($String[2]);
			$this->Stats['attributes']['intelligence'] 	= trim($String[3]);
			$this->Stats['attributes']['mind'] 			= trim($String[4]);
			$this->Stats['attributes']['piety'] 		= trim($String[5]);
		}
		
		// ELEMENTAL
		public function setElemental($String) 
		{ 
			$this->Stats['elemental']['fire'] 			= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['elemental']['ice'] 			= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['elemental']['wind'] 			= trim(filter_var($String[2], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['elemental']['earth'] 			= trim(filter_var($String[3], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['elemental']['lightning'] 		= trim(filter_var($String[4], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['elemental']['water'] 			= trim(filter_var($String[5], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > OFFENSE
		public function setOffense($String)
		{
			$this->Stats['offense']['accuracy'] 			= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['offense']['critical hit rate'] 	= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['offense']['determination'] 		= trim(filter_var($String[2], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > DEFENSE
		public function setDefense($String)
		{
			$this->Stats['defense']['defense'] 				= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['defense']['parry'] 				= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['defense']['magic defense'] 		= trim(filter_var($String[2], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > PHYSICAL
		public function setPhysical($String)
		{
			$this->Stats['physical']['attack power'] 		= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['physical']['skill speed'] 			= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > RESISTS
		public function setResists($String)
		{
			$this->Stats['resists']['slashing'] 			= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['resists']['piercing'] 			= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['resists']['blunt'] 				= trim(filter_var($String[2], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > SPELL
		public function setSpell($String)
		{
			$this->Stats['spell']['attack magic potency'] 	= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['spell']['healing magic potency']	= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['spell']['spell speed'] 			= trim(filter_var($String[2], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > CRAFTING
		public function setCrafting($String)
		{
			$this->Stats['crafting']['craftsmanship'] 	= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['crafting']['control']			= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > CRAFTING
		public function setGathering($String)
		{
			$this->Stats['gathering']['gathering'] 	= trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
			$this->Stats['gathering']['Perception']	= trim(filter_var($String[1], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// STATS > PVP
		public function setPVP($String)
		{
			$this->Stats['pvp']['morale'] = trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// GET STAT FUNC
		public function getStat($Type, $Attribute) { if (isset($this->Stats[$Type])) { return $this->Stats[$Type][$Attribute]; } else { return 0; }}
		public function getStats() { return $this->Stats; }
		
		// ACTIVE CLASS + LEVEL
		public function setActiveClassLevel($String)
		{
			$this->Stats['active']['level'] = trim(filter_var($String[0], FILTER_SANITIZE_NUMBER_INT));
		}
		
		// GEAR
		public function setGear($Array)
		{
			$this->Gear['slots'] = count($Array);
			$GearArray = NULL;
			
			// Loop through gear equipped
			$Main = NULL;
			foreach($Array as $A)
			{
				// Temp array
				$Temp = array();
				
				// Loop through data
				$i = 0;
				foreach($A as $Line)
				{
					// Item Icon
					if (stripos($Line, 'socket_64') !== false) { $Data = trim(explode('&quot;', $A[$i + 1])[1]); $Temp['icon'] = $Data; }
					if (stripos($Line, 'item_name') !== false) { $Data = trim(str_ireplace(array('>', '"'), NULL, strip_tags(html_entity_decode($A[$i + 2])))); $Temp['name'] = htmlspecialchars_decode(trim($Data), ENT_QUOTES); }
					if (stripos($Line, 'item_name') !== false) { 
						$Data = htmlspecialchars_decode(trim(html_entity_decode($A[$i + 3])), ENT_QUOTES);
						if (
							strpos($Data, " Arm") !== false || 
							strpos($Data, " Grimoire") !== false || 
							strpos($Data, " Tool") !== false
						) 
						{ $Main = $Data; $Data = 'Main'; }
						$Temp['slot'] = strtolower($Data);
					}

					// Item level
					if (stripos($Line, 'Item Level') !== false)
					{
						$int = filter_var(strip_tags(html_entity_decode($Line)), FILTER_SANITIZE_NUMBER_INT);
						$Temp['ilevel'] = $int;
					}

					
					// Increment
					$i++;
				}

				// Slot manipulation
				$Slot = $Temp['slot'];
				if (isset($GearArray['slots'][$Slot])) { $Slot = $Slot . 2; }	
				$Temp['slot'] = $Slot;	
				
				// Append array
				$GearArray['numbers'][] = $Temp;
				$GearArray['slots'][$Slot] = $Temp;
			}	
			
			// Set Gear
			$this->Gear['equipped'] = $GearArray;
			
			// Set Active Class
			$classjob = str_ireplace(array('Two-Handed ', 'One-Handed '), NULL, explode("'", $Main)[0]);
			$this->Stats['active']['class'] = $classjob;
			if (isset($this->Gear['equipped']['slots']['soul crystal'])) { $this->Stats['active']['job'] = str_ireplace("Soul of the ", NULL, $this->Gear['equipped']['slots']['soul crystal']); }
		}
		public function getGear()			{ return $this->Gear; }
		public function getEquipped($Type)	{ return $this->Gear['equipped'][$Type]; }
		public function getSlot($Slot)		{ return $this->Gear['equipped']['slots'][$Slot]; }
		public function getActiveClass() 	{ return $this->Stats['active']['class']; }
		public function getActiveJob() 		{ return isset($this->Stats['active']['job']) ? $this->Stats['active']['job'] : NULL; }
		public function getActiveLevel() 	{ return $this->Stats['active']['level']; }
		
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
					$arr['icon'] = trim(explode('&quot;', $Array[$i + 2])[1]);
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
					$arr['icon'] = trim(explode('&quot;', $Array[$i + 2])[1]);
					$Mounts[] = $arr;
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
					$Icon 	= isset(explode(" ", $A)[2]) ? explode('?', str_ireplace(array('"', 'src='), '', html_entity_decode(explode(" ", $A)[2])))[0] : null;
					$Class 	= strtolower(trim(strip_tags(html_entity_decode($Array[$i]))));
					$Level 	= trim(strip_tags(html_entity_decode($Array[$i + 1])));
					$EXP 	= trim(strip_tags(html_entity_decode($Array[$i + 2])));
					if ($Class)
					{
						$arr = array(
							'class' => $Class,
							'icon'	=> $Icon,
							'level' => $Level,
							'exp'	=> array(
								'current' => explode(" / ", $EXP)[0], 
								'max' => explode(" / ", $EXP)[1]
							)
						);
							
						$Temp[] = $arr;
						$Temp[$Class] = $arr;
					}
				}
				
				// Increment
				$i++;
			}
			
			$this->ClassJob = $Temp;
		}
		public function getClassJob($Class) { return $this->ClassJob[strtolower($Class)]; }
		public function getClassJobs($Specific = null) 
		{ 
			$arr = array();
			if ($Specific)
			{
				foreach($this->getClassJobs() as $Key => $Data)
				{
					if ($Specific == 'numbered')
					{
						if (is_numeric($Key)) 
						{
							$arr[] = $Data;
						}
					}
					else if ($Specific == 'named')
					{
						if (!is_numeric($Key)) 
						{
							$arr[$Key] = $Data;
						}
					}
				}
			}
			else
			{
				$arr = $this->ClassJob;
			}

			return $arr;
		}
		public function getClassJobsOrdered($Ascending = false, $Specific = NULL)
		{
			$ClassJobs = $this->getClassJobs();
			if ($Specific) { $ClassJobs = $this->getClassJobs($Specific); }
			$this->sksort($ClassJobs, "level", $Ascending);
			return $ClassJobs;
		}
		
		// VALIDATE
		public function validate()
		{
			// Check Name
			if (!$this->Name) 			{ $this->Validated = false; $this->Errors[] = 'Name is false'; }
			if (!$this->Server) 		{ $this->Validated = false; $this->Errors[] = 'Server is false'; }
			if (!$this->ID) 			{ $this->Validated = false; $this->Errors[] = 'ID is false'; }
			if (!$this->Lodestone) 		{ $this->Validated = false; $this->Errors[] = 'Lodestone URL is false'; }
			if (!$this->Avatars['96']) 	{ $this->Validated = false; $this->Errors[] = 'Avatars is false'; }
			
			if (!$this->Portrait) 		{ $this->Validated = false; $this->Errors[] = 'Portrait is false'; }
			if (!$this->Race) 			{ $this->Validated = false; $this->Errors[] = 'Race is false'; }
			if (!$this->Clan) 			{ $this->Validated = false; $this->Errors[] = 'Clan is false'; }
			if (!$this->Nameday) 		{ $this->Validated = false; $this->Errors[] = 'Nameday is false'; }
			if (!$this->Guardian) 		{ $this->Validated = false; $this->Errors[] = 'Guardian is false'; }
			if (!$this->City) 			{ $this->Validated = false; $this->Errors[] = 'City is false'; }
			
			if (!is_numeric($this->Stats['core']['hp'])) { $this->Validated = false; $this->Errors[] = 'hp is false or non numeric'; }
			if (!is_numeric($this->Stats['core']['mp'])) { $this->Validated = false; $this->Errors[] = 'mp is false or non numeric'; }
			if (!is_numeric($this->Stats['core']['tp'])) { $this->Validated = false; $this->Errors[] = 'tp is false or non numeric'; }
			
			foreach($this->ClassJob as $CJ)
			{
				if (!is_numeric($CJ['level']) && $CJ['level'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
				if (!is_numeric($CJ['exp']['current']) && $CJ['exp']['current'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
				if (!is_numeric($CJ['exp']['max']) && $CJ['exp']['current'] != '-') { $this->Validated = false; $this->Errors[] = $CJ['class'] .' level was non numeric and not "-"'; }
			}
		}
		public function isValid() { return $this->Validated; }
		public function getErrors() { return $this->Errors; }
	}

	/* Free Company
	 * ------------
	 */	
	class FreeCompany
	{
		private $ID;
		private $Company;
		private $Name;
		private $Server;
		private $Tag;
		private $Formed;
		private $MemberCount;
		private $Slogan;

		private $Members = [];

		#-------------------------------------------#
		# FUNCTIONS									#
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
		public function setNameServerCompany($String)
		{
			$this->Company 	= trim(explode("&lt;", explode("friendship_color", $String[9])[0])[0]);
			$this->Name 	= trim(htmlspecialchars_decode(trim($String[10]), ENT_QUOTES));
			$this->Server 	= trim(str_ireplace(array("(", ")"), null, htmlspecialchars_decode(trim($String[11]), ENT_QUOTES)));
		}

		// TAG + FORMED + MEMBERS + SLOGAN
		public function setCompanyDetails($String)
		{
			$this->Tag 			= Trim(str_ireplace("&raquo;", null, strip_tags(htmlspecialchars_decode(explode("|", str_ireplace("laquo;", "|", $String[9]))[1]))));
			$this->Formed 		= trim(explode(",", explode("(", $String[16])[2])[0]);
			$this->MemberCount 	= trim(strip_tags(htmlspecialchars_decode(trim($String[22]), ENT_QUOTES)));
			$this->Slogan 		= trim(strip_tags(htmlspecialchars_decode(trim($String[26]), ENT_QUOTES)));
		}
		public function getCompany() { return $this->Company; }
		public function getName() { return $this->Name; }
		public function getServer() { return $this->Server; }
		public function getTag() { return $this->Tag; }
		public function getFormed() { return $this->Formed; }
		public function getMemberCount() { return $this->MemberCount; }
		public function getSlogan() { return $this->Slogan; }

		// MEMBERS / PARSE + SET + GET
		public function parseMembers($Data)
		{
			// Temp array
			$temp = [];

			// Loop through data
			foreach($Data as $D)
			{
				$Name 		= trim(explode("(", trim(strip_tags(htmlspecialchars_decode($D[1]), ENT_QUOTES)))[0]);
				$Server 	= trim(str_ireplace(")", "", trim(explode("(", trim(strip_tags(htmlspecialchars_decode($D[1]), ENT_QUOTES)))[1])));
				$ID 		= trim(explode("/", $D[1])[3]);

				$RankImage	= trim(explode("?", explode("&quot;", $D[3])[1])[0]);
				$Rank		= trim(str_ireplace("&gt;", null, explode("&quot;", $D[3])[8]));

				$ClassImage = explode("?", explode("&quot;",$D[7])[3])[0];
				$ClassLevel = explode(">", strip_tags(htmlspecialchars_decode(explode("&quot;",$D[7])[10])))[1];

				$arr =
				[
					'id'		=> $ID,
					'name'		=> $Name,
					'server'	=> $Server,

					'rank' =>
					[
						'image' => $RankImage,
						'title' => $Rank
					],

					'class' =>
					[
						'image' => $ClassImage,
						'level' => $ClassLevel,
					]
				];
				
				// Append to array
				$temp[] = $arr;
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


	/* Linkshell
	 * ---------
	 */	
	class Linkshell
	{
		private $ID;
		private $Name;
		private $Server;
		private $TotalMembers;

		private $Members = [];

		#-------------------------------------------#
		# FUNCTIONS									#
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
			$this->Name 	= trim(explode("(", $String[0])[0]);
			$this->Server 	= trim(str_ireplace(")", null, explode("(", $String[0])[1]));
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
			$temp = [];

			// Loop through members
			foreach($Array as $arr)
			{
				// Rank can move offset. Take it out, process it and remove it
				if (stripos($arr[9], "ic_") !== false)
				{
					$Rank = isset(explode("&quot;", $arr[9])[1]) ? trim(explode("&quot;", $arr[9])[1]) : null;
					switch($Rank)
					{
						default: $Rank = 'member'; break;
						case 'ic_master': $Rank = 'master'; break;
						case 'ic_leader': $Rank = 'leader'; break;
					}
					$arr[9] = null;
					$arr = array_values(array_filter($arr));
				}
				else
				{
					// Default rank
					$Rank = 'member';
				}

				// Char data
				$ID 				= trim(explode("/", $arr[1])[3]);
				$Avatar 			= trim(explode("?", explode("&quot;", $arr[2])[1])[0]);
				$Name 				= trim(explode("(", strip_tags(htmlspecialchars_decode($arr[8])))[0]);
				$Server				= trim(explode("(", str_ireplace(")", null, strip_tags(htmlspecialchars_decode($arr[8]))))[1]);

				// Class
				$ClassIcon			= trim(explode("&quot;", $arr[12])[3]);
				$ClassLevel 		= intval(trim(strip_tags(htmlspecialchars_decode($arr[13]))));

				// Company
                $CompanyName = null; $CompanyRank = null;
                $CompanyIcon        = isset(explode("&quot;", $arr[15])[1]) ? trim(explode("&quot;", $arr[15])[1]) : null;
                if ($CompanyIcon)
                {
                    $CompanyName    = trim(explode("/", str_ireplace("-->", null, strip_tags(htmlspecialchars_decode($arr[15]))))[0]);
                    $CompanyRank    = trim(explode("/", str_ireplace("-->", null, strip_tags(htmlspecialchars_decode($arr[15]))))[1]);
                }

                // Free Company (fixed by @stygiansabyss for patch 2.1)
                if ($CompanyIcon) 
                {
                    $freeCompanyDetails      = 24;
                    $freeCompanyImagesFirst  = $arr[20];
                    $freeCompanyImagesSecond = $arr[21];
                    $freeCompanyImagesThird  = $arr[22];
                } 
                else 
                {
                    $freeCompanyDetails      = 23;
                    $freeCompanyImagesFirst  = $arr[19];
                    $freeCompanyImagesSecond = $arr[20];
                    $freeCompanyImagesThird  = $arr[21];
                }
                $FC_ID = null; $FC_Name = null;
                $FC_Icon = array();
                $FC_Icon[]          = isset(explode("&quot;", $freeCompanyImagesFirst)[1]) ? trim(explode("&quot;", $freeCompanyImagesFirst)[1]) : null;
                $FC_Icon[]          = isset(explode("&quot;", $freeCompanyImagesSecond)[1]) ? trim(explode("&quot;", $freeCompanyImagesSecond)[1]) : null;
                $FC_Icon[]          = isset(explode("&quot;", $freeCompanyImagesThird)[1]) ? trim(explode("&quot;", $freeCompanyImagesThird)[1]) : null;
                if ($FC_Icon[0] != null)
                {
                    if ($FC_Icon[2] == null) {
                        $freeCompanyDetails--;
                        unset($FC_Icon[2]);
                    }
                    if ($FC_Icon[1] == null) {
                        $freeCompanyDetails--;
                        unset($FC_Icon[1]);
                    }
                    if ($FC_Icon[0] == null) {
                        $freeCompanyDetails--;
                    }

                    $FC_ID          = trim(explode("/", explode("&quot;", $arr[$freeCompanyDetails])[3])[3]);
                    $FC_Name        = trim(str_ireplace("-->", null, strip_tags(htmlspecialchars_decode($arr[$freeCompanyDetails]))));
                }

				// Sort array
				$arr =
				[
					'id'		=> $ID,
					'avatar'	=> $Avatar,
					'name'		=> $Name,
					'server'	=> $Server,
					'rank'		=> $Rank,

					'class' =>
					[
						'icon'	=> $ClassIcon,
						'level'	=> $ClassLevel,
					],
					
					'company' =>
					[
						'icon'	=> $CompanyIcon,
						'name'	=> $CompanyName,
						'rank'	=> $CompanyRank,
					],
					
					'freecompany' =>
					[
						'icon'	=> $FC_Icon,
						'id'	=> $FC_ID,
						'name'	=> $FC_Name,
					],
				];

				// append to temp array
				$temp[] = $arr;
			}

			// Set Members
			$this->Members = $temp;
		}
		public function getMembers() { return $this->Members; }
	}
	
	
	/*	Achievement
	 *	-----------
	 */
	class Achievements
	{
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
				$NewList[] = $Temp;
			}

			// Set points percentage
			$this->PointsPercentage = (round($this->CurrentPoints / $this->TotalPoints, 3) * 100);
			
			// Set Achievement List
			$this->List = $NewList;	
		}
		public function get() { return $this->List; }
		public function addAchievements($List) { $this->List = array_merge($this->List, $List); }
		public function genPointsPercentage() { $this->PointsPercentage = (round($this->CurrentPoints / $this->TotalPoints, 3) * 100); }
	}
	
	/*	Parser
	 *	------
	 *	> getSource - $URL [protected] (Fetches the source code of the specified url.)
	 *	> curl - $URL [private] (Core curl function with additional options.)
	 */
	class Parser
	{
		// The source code of the most recent curl
		protected $SourceCodeArray;
		
		// Find data based on a tag
		protected function find($Tag, $Clean = TRUE)
		{
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
			$Found 		= false;
			$Found2		= false;
			$Interates 	= 0;
			$Array 		= NULL;
			
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
			$Found 		= false;
			$Found2		= false;
			$Interates 	= 0;
			$Array 		= NULL;
			$Array2		= NULL;
			
			// If range null
			if (!$Range) { $Range = 9999; }
			
			// Search for element
			foreach($this->SourceCodeArray as $Line)
			{
				// Trim line
				$Line = trim($Line);
				
				// Search line, mark found
				if(stripos($Line, $Tag) !== false && $Tag) { $Found = true; }
				if(stripos($Line, $Tag2) !== false && $Tag2) { $Found2 = true; }

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
						$Found 		= false;
						$Found2 	= false;
						$Interates 	= 0;
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
			// Strip tags
			$Line = strip_tags(html_entity_decode($Line));
			
			// Random removals
			$Remove = array("-->");
			$Line = str_ireplace($Remove, NULL, $Line);

			// Return value
			return $Line;
		}
		
		// Prints the source array
		public function printSourceArray()
		{
			show($this->SourceCodeArray);
		}
		
		// Get the DOMDocument from the source via its URL.
		protected function getSource($URL)
		{
			// Get the source of the url
			# Show($URL);
			$Source = $this->curl($URL);
			$this->SourceCodeArray = explode("\n", $Source);
			return true;	
		}
		
		// Fetches page source via CURL
		protected function curl($URL)
		{
			$options = array(	
				CURLOPT_RETURNTRANSFER	=> true,         	// return web page
				CURLOPT_HEADER         	=> false,        	// return headers
				CURLOPT_FOLLOWLOCATION 	=> false,        	// follow redirects
				CURLOPT_ENCODING       	=> "",     			// handle all encodings
				CURLOPT_AUTOREFERER    	=> true,         	// set referer on redirect
				CURLOPT_CONNECTTIMEOUT 	=> 15,           	// timeout on connects
				CURLOPT_TIMEOUT        	=> 15,           	// timeout on response
				CURLOPT_MAXREDIRS      	=> 5,            	// stop after 10 redirects
				CURLOPT_USERAGENT      	=> "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36", 
				CURLOPT_HTTPHEADER     	=> array('Content-type: text/html; charset=utf-8', 'Accept-Language: en'),
			);
			
			$ch = curl_init($URL);	
			curl_setopt_array($ch, $options);	
			$source = curl_exec($ch);
			curl_close($ch);
			return htmlentities($source);	
		}
	}	

	#-----------------------------------------------
	# Example Usage
	#-----------------------------------------------
	/*
	
	# New API
	$API = new LodestoneAPI();

	# Parse Linkshell
	$Linkshell = $API->getLS(
	[
		"name"		=> "ComraderyARR",
		"server"	=> "Excalibur",
	]);
	Show($Linkshell);	
	

	$API = new LodestoneAPI();

	# Parse Free Company
	$FreeCompany = $API->getFC(
	[
		"name" 		=> "Penguin Farm", 
		"server" 	=> "Excalibur"
	],
	[
		"members"	=> true,
	]);
	Show($FreeCompany); // returned object
	*/
/*
	$API = new LodestoneAPI();

	# Parse Character
	$Character = $API->get(
	[
		"name"		=> "Premium Virtue",
		"server"	=> "Excalibur"
	]);
	Show($Character);
	$API->printSourceArray();


	// Set an ID
	$API = new LodestoneAPI();

	// Parse achievements
	$API->parseAchievements(730968);

	Show($API->getAchievements());
	
	// Show achievements
	Show($API->getAchievements());


	// Lodestone
	$API = new LodestoneAPI();
	
	// Set category id
	$CategoryID = 2;

	// Parse achievement by category
	$API->parseAchievementsByCategory($CategoryID, 730968);

	// Get achievements
	Show($API->getAchievements()[$CategoryID]);
	
	/*
	# Parse Character
	show("> new LodestoneAPi");
	$API = new LodestoneAPI();

	show("> get");
	$Character = $API->get(
	[
		"name"		=> "Premium Virtue",
		"server"	=> "Excalibur"
	]);

	show("> vardump");
	show($Character->getMounts());

	$API = new LodestoneAPI();
	$Options = 
	[
		'topics' => true,
	];

	$Lodestone = $API->Lodestone($Options);

	Show($Lodestone->getTopics());
	*/

?>
