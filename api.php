<?
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
 	# error_reporting(-1);
	function show($Data) { echo '<pre>'; print_r($Data); echo '</pre>'; }

	/*	LodestoneAPI
	 *	------------
	 */
	class LodestoneAPI extends Parser
	{
		// url addresses to various lodestone content.
		private $URL = array(
			'profile'		=> 'http://na.beta.finalfantasyxiv.com/lodestone/character/',
			'achievement' 	=> '/achievement/',
			'search'		=> '?q=%name%&worldname=%server%'
		);
		
		// Configuration
		public $AchievementCategories = array(13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24);
		public $ClassList = array();
		public $ClassDisicpline = array();
		
		// List of characters parsed.
		public $Characters 		= array();
		public $Achievements 	= array();
		public $Search 			= array();
		
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
		public function get($Name, $Server)
		{
			// Clean
			$Name 	= trim(ucwords($Name));
			$Server = trim(ucwords($Server));
			
			// Search by Name + Server, exact
			$this->searchCharacter($Name, $Server, true);
			
			// Get by specific ID
			$ID = $this->getSearch()['results'][0]['id'];
			
			// If an ID or not
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
				$this->getSource($this->URL['profile'] . str_ireplace(array('%name%', '%server%'), array(str_ireplace(" ", "+", $Name), $Server), $this->URL['search']));
				
				// Get all found characters
				$Found = $this->findAll('thumb_cont_black_50', 5, NULL, false);
				
				// Loop through results
				if ($Found)
				{
					foreach($Found as $F)
					{
						$Avatar 	= explode('&quot;', $F[0])[3];
						$Data 		= explode('&quot;', $F[3]);
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
							#Show($Character['name'] .' < > '. $ExactName);
							#Show(md5($Character['name']) .' < > '. md5($ExactName));
							#Show(strlen($Character['name']) .' < > '. strlen($ExactName));
							if (($Character['name']) == ($ExactName))
							{
								$this->Search['results'] = NULL;
								$this->Search['results'][] = $Character;
								$this->Search['isExact'] = true;
								break;
							}
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
		
		#-------------------------------------------#
		# PROFILE									#
		#-------------------------------------------#
		
		// Parse a profile based on ID.
		public function parseProfile($ID)
		{
			if (!$ID)
			{
				echo "error: No ID Set.";	
			}
			else
			{
				// Get the source
				$this->getSource($this->URL['profile'] . $ID);	
				
				// Create a new character object
				$Character = new Character();
				
				// Set Character Data
				$Character->setID(trim($ID), $this->URL['profile'] . $ID);
				$Character->setNameServer($this->find('player_name_brown'));
				$Character->setAvatar($this->find('thumb_cont_black_40', false));
				$Character->setPortrait($this->findRange('bg_chara_264', 2, NULL, false));
				$Character->setRaceClan($this->find('chara_profile_title'));
				$Character->setLegacy($this->find('bt_legacy_history'));
				$Character->setBirthGuardianCompany($this->findRange('chara_profile_list', 60, NULL, false));
				$Character->setCity($this->findRange('City-state', 5));
				$Character->setBiography($this->findRange('txt_selfintroduction', 5));
				$Character->setHPMPTP($this->findRange('param_power_area', 10));
				$Character->setAttributes($this->findRange('param_list_attributes', 8));
				$Character->setElemental($this->findRange('param_list_elemental', 8));
				$Character->setOffense($this->findRange('param_title_offence', 6));
				$Character->setDefense($this->findRange('param_title_deffence', 6));
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
				
				#$this->segment('class_fighter');
				
				// Set ClassJob
				$Character->setClassJob($this->findRange('class_fighter', NULL, '//Class Contents', false));
				
				// Validate data
				$Character->validate();
				
				// Append character to array
				$this->Characters[$ID] = $Character;
			}
		}
		
		// Parse just biography, based on ID.
		public function parseBiography($ID)
		{
			// Get the source
			$this->getSource($this->URL['profile'] . $ID);	
			
			// Create a new character object
			$Character = new Character();
			
			// Get biography
			$Character->setBiography($this->findRange('txt_selfintroduction', 5));
			
			// Append character to array
			$this->Characters[$ID] = $Character;
		}
		
		// Get a list of parsed characters.
		public function getCharacters() { return $this->Characters;	}
		
		// Get a list of parsed characters.
		public function getCharacterByID($ID) { return $this->Characters[$ID]; }
		
		#-------------------------------------------#
		# ACHIEVEMENTS								#
		#-------------------------------------------#
		
		// Get a list of parsed characters
		public function getAchievements() { return $this->Achievements; }
		
		// Parse a achievements based on ID
		public function parseAchievements($ID)
		{
			if (!$ID)
			{
				echo "error: No ID Set.";	
			}
			else
			{
				// Loop through categories
				foreach($this->AchievementCategories as $Category)
				{
					// Get the source
					$x = $this->getSource($this->URL['profile'] . $ID . $this->URL['achievement'] .'category/'. $Category .'/');
					
					// Create a new character object
					$Achievements = new Achievements();
					
					// Get Achievements
					$Achievements->set($this->findAll('achievement_area_body', NULL, 'bt_more', false));
					$Achievements->setPoints($this->findRange('total_point', 10));
					$Achievements->setCategory($Category);
					
					// Append character to array
					$this->Achievements[$ID][$Category] = $Achievements;
				}
			}
		}
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
			$String 		= explode(" (", str_ireplace(")", NULL, $String));
			$this->Name 	= htmlspecialchars_decode(trim($String[0]), ENT_QUOTES);
			$this->Server 	= trim($String[1]);
			$this->NameClean= preg_replace('/[^a-z]/i', '', strtolower($this->Name));
			
		}
		public function getName() { return $this->Name; }
		public function getServer() { return $this->Server; }
		public function getNameClean() { return$this->NameClean; }
		
		// AVATAR
		public function setAvatar($String)
		{
			$this->Avatars['50'] = trim(explode('&quot;', $String)[3]);
			$this->Avatars['64'] = str_ireplace("50x50", "60x60", $this->Avatars['50']);
			$this->Avatars['96'] = str_ireplace("50x50", "96x96", $this->Avatars['50']);
		}
		public function getAvatar($Size) { return $this->Avatars[$Size]; }
		
		// PORTRAIT
		public function setPortrait($String)
		{
			$this->Portrait = trim(explode('&quot;', $String[1])[1]);
		}
		public function getPortrait() { return $this->Portrait; }
		
		// RACE + CLAN
		public function setRaceClan($String)
		{
			$String 		= explode("/", $String);
			$this->Clan 	= htmlspecialchars_decode(trim($String[0]), ENT_QUOTES);
			$this->Race 	= htmlspecialchars_decode(trim($String[1]), ENT_QUOTES);
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
			$this->Guardian 	= trim(strip_tags(html_entity_decode($String[15])));
				
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
			foreach($Array as $A)
			{
				// Temp array
				$Temp = array();
				
				// Loop through data
				$i = 0;
				$Main;
				foreach($A as $Line)
				{
					// Item Icon
					if (stripos($Line, 'socket_64') !== false) { $Data = trim(explode('&quot;', $A[$i + 1])[1]); $Temp['icon'] = $Data; }
					if (stripos($Line, 'item_name') !== false) { $Data = trim(str_ireplace(array('>', '"'), NULL, strip_tags(html_entity_decode($A[$i + 2])))); $Temp['name'] = htmlspecialchars_decode(trim($Data), ENT_QUOTES); }
					if (stripos($Line, 'item_name') !== false) { 
						$Data = htmlspecialchars_decode(trim(html_entity_decode($A[$i + 3])), ENT_QUOTES);
						if (strpos($Data, " Arm") !== false || strpos($Data, " Tool") !== false) { $Main = $Data; $Data = 'Main'; }
						$Temp['slot'] = strtolower($Data);
					}
					
					// Increment
					$i++;
				}
				
				// Slot manipulation
				$Slot = $Temp['slot'];
				if (isset($GearArray[$Slot])) { $Slot = $Slot . 2; }		
				
				// Append array
				$GearArray['numbers'][] = $Temp;
				$GearArray['slots'][$Slot] = $Temp;
			}	
			
			// Set Gear
			$this->Gear['equipped'] = $GearArray;
			
			// Set Active Class
			$classjob = str_ireplace('Two--Handed ', NULL, explode("'", $Main)[0]);
			$this->Stats['active']['class'] = $classjob;
			if (isset($this->Gear['soul crystal'])) { $this->Stats['active']['job'] = str_ireplace("Soul of the ", NULL, $this->Gear['soul crystal']['name']); }
		}
		public function getGear()			{ return $this->Gear; }
		public function getEquipped($Type)	{ return $this->Gear['equipped'][$Type]; }
		public function getSlot($Slot)		{ return $this->Gear['equipped']['slots'][$Slot]; }
		public function getActiveClass() 	{ return $this->Stats['active']['class']; }
		public function getActiveJob() 		{ return $this->Stats['active']['job']; }
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
					$arr['Name'] = trim(explode('&quot;', $Array[$i])[5]);
					$arr['Icon'] = trim(explode('&quot;', $Array[$i + 2])[1]);
					$Pets[] = $arr;
				}
				
				// Increment
				$i++;		
			}
			
			// set pets
			$this->Minions = $Pets;
		}
		public function getMinions() { return $this->Minions; }
		
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
					$Class 	= trim(strip_tags(html_entity_decode($Array[$i])));
					$Level 	= trim(strip_tags(html_entity_decode($Array[$i + 1])));
					$EXP 	= trim(strip_tags(html_entity_decode($Array[$i + 2])));
					if ($Class)
					{
						$arr = array(
							'class' => $Class,
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
		public function getClassJob($Class) { return $this->ClassJob[ucwords($Class)]; }
		
		// VALIDATE
		public function validate()
		{
			// Check Name
			if (!$this->Name) { $this->Validated = false; $this->Errors[] = 'Character name is false.'; }
		}
		public function isValid() { return $this->Validated; }
		public function getErrors() { return $this->Errors; }
		
	}		
	
	
	/*	Achievement
	 *	-----------
	 */
	class Achievements
	{
		private $Category;
		private $TotalPoints;
		private $Points;
		private $List;
		
		// CATEGORIES
		public function setCategory($ID)
		{
			$this->Category = $ID;	
		}
		public function getCategory() { return $this->Category; }
		
		// POINTS
		public function setPoints($String)
		{
			$this->TotalPoints = trim($String[0]);	
		}
		public function getPoints() { return $this->TotalPoints; }
		
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
					if (stripos($Line, 'achievement_name') !== false) { $Data = trim(strip_tags(html_entity_decode($Line))); $Temp['name'] = $Data; }
					if (stripos($Line, 'achievement_point') !== false) { $Data = trim(strip_tags(html_entity_decode($Line))); $Temp['points'] = $Data; }
					if (stripos($Line, 'getElementById') !== false) { $Temp['date'] = trim(filter_var(explode("(", strip_tags(html_entity_decode($Line)))[2], FILTER_SANITIZE_NUMBER_INT)); }
					
					// Increment
					$i++;
				}
				
				// Obtained or not
				if ($Temp['date']) { $Temp['obtained'] = true; } else { $Temp['obtained'] = false; }
				
				// Increment Points
				if ($Temp['obtained']) { $this->Points['current'] += $Temp['points']; }
				$this->Points['max'] += $Temp['points'];
				
				// Append temp data
				$NewList[] = $Temp;
			}
			
			// Set Achievement List
			$this->List = $NewList;	
		}
		public function get() { return $this->List; }
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
		protected function findRange($Tag, $Range,$Tag2 = NULL, $Clean = TRUE)
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
					// If clean true, clean line!
					if ($Clean) { $Array[] = $this->Clean(strip_tags(html_entity_decode($Line))); } else { $Array[] = $Line; }
					
					// Iterate
					$Interates++;
					
					// If iterate hits range, break.
					if ($Interates == $Range  || $Found2) { break; }
				}
			}
			
			// Remove empty values
			$Array = array_values(array_filter($Array));
			
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
		private function clean($Line)
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
			Show($this->SourceCodeArray);
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
		private function curl($URL)
		{
			$options = array(	
				CURLOPT_RETURNTRANSFER	=> true,         	// return web page
				CURLOPT_HEADER         	=> false,        	// return headers
				CURLOPT_FOLLOWLOCATION 	=> false,        	// follow redirects
				CURLOPT_ENCODING       	=> "",     			// handle all encodings
				CURLOPT_AUTOREFERER    	=> true,         	// set referer on redirect
				CURLOPT_CONNECTTIMEOUT 	=> 60,           	// timeout on connects
				CURLOPT_TIMEOUT        	=> 60,           	// timeout on response
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
	
	# --------------------------------------


	// Setup API
	$API = new LodestoneAPI();
	
	# $API->searchCharacter("P'r'e'm'i'u'm V'i't'", "Hyperion");
	# Show($API->getSearch());
	
	// Set character object (echo portrait for example)
	$Character = $API->get("Darka Munday", "Ragnarok");
	Show("class = " . $Character->getActiveClass());
	Show("job = ". $Character->getActiveJob());
	Show("level = ". $Character->getActiveLevel());
	# echo '<img src="'. $Character->getPortrait() .'" />';
	
	// Search by Name + Server
	# $API->searchCharacter("Premium Virtue", "Gungnir", true);
	
	// Get by specific ID
	# $ID = 730968;
	# $API->parseProfile($ID);
	# $Character = $API->getCharacterByID($ID);
	# Show($Character);
	
	// Get by specific ID
	# $API->parseAchievements($ID);
	# Show($API->getAchievements());

	// Print source code (for debugging)
	# $API->printSourceArray();

	
?>