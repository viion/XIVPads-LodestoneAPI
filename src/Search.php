<?php
namespace Viion\Lodestone;

class Search
{
    use Urls;
    use Data;
    use Funky;

    // Set true to use basic parsing, otherwise uses advanced regex
    private $basicParsing = false;

    /**
     * Switch to basic searching method
     */
    public function useBasicParsing()
    {
        $this->basicParsing = true;
    }

    /**
     * Search for a character, either by name/world or ID.
     *
     * @param $nameOrId - the name or id of the character
     * @param $world - the world/server of the character, string
     * @return Character - new character object.
     */
    public function Character($nameOrId, $world = null)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($nameOrId))
        {
            if ($this->basicParsing) {
                $character = $this->basicCharacterSearch($nameOrId);
            } else {
                $character = $this->advancedCharacterSearch($nameOrId);
            }

            // basic check
            if (empty($character->name)) {
                return false;
            }

            return $character;
        }
        else
        {

            $searchName = str_ireplace(' ', '+',  $nameOrId);

            // Generate url
            $url = $this->urlGen('characterSearch', [ '{name}' => $searchName, '{world}' => $world ]);
            $html = $this->trim($this->curl($url), '<!-- result -->', '<!-- /result -->');

            $p = new Parser($html);

            // go through results
            foreach($p->findAll('thumb_cont_black_50', 'col3box_right') as $i => $node) {
                $node = new Parser($node);

                $data = explode(' (', $node->find('player_name_gold', 0)->text());

                // Character
                $id     = filter_var($node->find('player_name_gold', 0)->attr('href'), FILTER_SANITIZE_NUMBER_INT);
                $name   = trim($data[0]);
                $world  = trim(str_replace(')', null, $data[1]));
                $avatar = explode('?', $node->find('thumb_cont_black_50', 1)->attr('src'))[0];

                // match what was sent (lower both as could be user input)
                if (strtolower($name) == strtolower($nameOrId) && is_numeric($id)) {
                    // recurrsive callback
                    return $this->Character($id);
                }
            }

            return false;
        }
    }

    /**
     * Search for a freecompany
     *
     * @param $nameOrId
     * @param $member
     */
    public function Freecompany($nameOrId, $member = null)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($nameOrId)) {
            // If basic searching
//            if ($this->basicParsing) {
//                return $this->basicFreecompanySearch($nameOrId);
//            }

            // Advanced searching
            return $this->advancedFreecompanyParse($nameOrId,$member);

        }
    }

    /**
     * Search for a linkshell
     *
     * @param $nameOrId
     */
    public function Linkshell($nameOrId)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($nameOrId)) {
            // If basic searching
//            if ($this->basicParsing) {
//                return $this->basicFreecompanySearch($nameOrId);
//            }

            // Advanced searching
            return $this->advancedLinkshellParse($nameOrId);

        }
    }

    /**
     * Parse character data, does it using basic methods, Slower.
     *
     * @param $characterId - character id
     * @return Array - character data
     */
    private function basicCharacterSearch($characterId)
    {
        // New character object
        $character = new Character();

        // Setup url and get html
        $url = $this->urlGen('characterProfile', [ '{id}' => $characterId ]);
        $html = $this->trim($this->curl($url), '<!-- contents -->', '<!-- //Minion -->');

        $p = new Parser($html);

        $character->id = $p->find('player_name_thumb', 1)->numbers();
        $character->name = $p->find('player_name_thumb', 4)->text();
        $character->world = $p->find('player_name_thumb', 5)->text();
        $character->title = $p->find('chara_title')->text();
        $character->avatar = $p->find('player_name_thumb', 2)->attr('src');
        $character->avatarLarge = str_ireplace('50x50', '96x96', $character->avatar);
        $character->avatarMedium = str_ireplace('50x50', '64x64', $character->avatar);

        $character->portrait = $p->find('bg_chara_264', 1)->attr('src');
        $character->portraitLarge = str_ireplace('264x360', '640x873', $character->portrait);

        $character->bio = $p->find('txt_selfintroduction', 1)->text();
        $character->race = explode('/', $p->find('chara_profile_title')->text())[0];
        $character->clan = explode('/', $p->find('chara_profile_title')->text())[1];
        $character->gender = explode('/', $p->find('chara_profile_title')->text())[2];

        $character->nameday = $p->find('chara_profile_left', 6)->text();
        $character->guardian = $p->find('chara_profile_left', 8)->text();
        $character->guardianIcon = $p->find('chara_profile_left', 4)->attr('src');
        $character->city = $p->find('chara_profile_left', 12)->text();
        $character->cityIcon = $p->find('chara_profile_left', 10)->attr('src');
        $character->grandCompany = explode('/', $p->find('chara_profile_left',16)->text())[0];

        if ($character->grandCompany[0] == '-') {
            $character->grandCompany = null;
        }

        if ($character->grandCompany)
        {
            $character->grandCompanyRank = explode('/', $p->find('chara_profile_left',16)->text())[1];
            $character->grandCompanyIcon = $p->find('chara_profile_left', 14)->attr('src');
        }
        $character->freeCompany = $p->find('ic_crest_32', 6)->text();

        // Only proceed if caracter is in an Fc
        if ($character->freeCompany) {
            $character->freeCompanyId = filter_var($p->find('ic_crest_32', 6)->attr('href'), FILTER_SANITIZE_NUMBER_INT);
            $character->freeCompanyIcon = [
                $p->find('ic_crest_32', 2)->attr('src'),
                $p->find('ic_crest_32', 3)->attr('src'),
                $p->find('ic_crest_32', 4)->attr('src')
            ];
        }

        # Class/Jobs

        $character->activeLevel = $p->find('"level"')->numbers();

        foreach($p->findAll('ic_class_wh24_box', 3, 'base_inner') as $i => $node) {
            // new node
            $node = new Parser($node);
            $name = $node->find('ic_class_wh24_box')->text();

            if ($name) {
                $exp    = explode(' / ', $node->find('ic_class_wh24_box', 2)->text());
                $icon   = explode('?', $node->find('ic_class_wh24_box')->attr("src"))[0];
                $level  = $node->find('ic_class_wh24_box', 1)->numbers();

                if (!$level) {
                    $level = 0;
                }

                $character->classjobs[] = [
                    'icon' => $icon,
                    'name' => $name,
                    'level' =>  $level,
                    'exp_current' => intval($exp[0]),
                    'exp_level' => intval($exp[1]),
                ];
            }

            unset($node);
        }

        # Gear

        $iLevelTotal = 0;
        $iLevelArray = [];
        $iLevelCalculated = [];
        foreach($p->findAll('item_detail_box', 35, 'param_power_area') as $i => $node) {
            // new node
            $node = new Parser($node);

            $ilv = filter_var($node->find('pt3 pb3')->text(), FILTER_SANITIZE_NUMBER_INT);
            $slot = $node->find('category_name')->text();
            $name = str_ireplace('">', null, $node->find('category_name', -1)->text());

            $character->gear[] = [
                'icon'  => explode('?', $node->find('itemicon')->attr('src'))[0],
                'color' => explode('_', $node->find('category_name', -2)->text())[0],
                'name'  => $name,
                'slot'  => $slot,
                'id'    => explode('/', $node->find('bt_db_item_detai', 1)->html())[5],
                'ilv'   => $ilv ,
            ];

            if ($slot != 'Soul Crystal') {
                $iLevelTotal = $iLevelTotal + $ilv;
                $iLevelArray[] = $ilv;
                $iLevelCalculated[] = $ilv;

                if (in_array($slot, $this->getTwoHandedItems())) {
                    $iLevelCalculated[] = $ilv;
                }
            }

            // active class
            if ($i == 0) {
                $character->activeClass = explode("'", str_ireplace(['Two-Handed ', 'One-Handed'], null, $slot))[0];
            }

            // active job
            if ($slot == 'Soul Crystal') {
                $character->activeJob = str_ireplace('Soul of the ', null, $name);
            }
        }

        $character->gearStats = [
            'total' => $iLevelTotal,
            'average' => floor(array_sum($iLevelCalculated) / 13),
            'array' => $iLevelArray,
        ];

        # Attributes

        // Setting defaults to avoid undefined indexes
        // 25.02.2014 @JohnRamboTSQ
        $character->attributes = [
            'hp' => null,
            'mp' => null,
            'cp' => null,
            'gp' => null,
            'tp' => null,
            'attack-magic-potency' => null,
            'healing-magic-potency' => null,
            'spell-speed' => null,
            'craftsmanship' => null,
            'control' => null,
            'gathering' => null,
            'perception' => null
        ];

        $character->attributes['hp'] = $p->find('class="hp"')->numbers();
        $character->attributes['mp'] = $p->find('class="mp"')->numbers();
        $character->attributes['tp'] = $p->find('class="tp"')->numbers();
        $character->attributes['cp'] = $p->find('class="cp"')->numbers();
        $character->attributes['gp'] = $p->find('class="gp"')->numbers();

        foreach($p->findAll('param_list_attributes', 6) as $i => $node) {
            // new node
            $node = new Parser($node);
            $attr = ['str', 'dex', 'vit', 'int', 'mnd'];

            foreach($attr as $a) {
                $character->attributes[$a] = $node->find($a)->numbers();
            }
        }

        foreach($p->findAll('param_list_elemental', 8) as $i => $node) {
            // new node
            $node = new Parser($node);
            $attr = ['fire', 'ice', 'wind', 'earth', 'thunder', 'water'];

            foreach($attr as $a) {
                $character->attributes[$a] = $node->find($a)->numbers();
            }
        }

        foreach($p->findAll('param_list', 10, 'param_list_elemental') as $i => $node) {
            // new node
            $node = new Parser($node);

            foreach($node->findAll('clearfix',1) as $j => $n)
            {
                $n = new Parser($n);
                $name = strtolower(str_replace(range(0,9), null, $n->find('clearfix')->text()));
                $name = str_replace(' ', '-', trim($name));

                if ($name) {
                    $value = $n->find('clearfix')->numbers();
                    $character->attributes[$name] = intval(trim($value));
                }
            }
        }

        # Minions and Mounts
        foreach($p->findAll('minion_box', 'chara_content_title') as $i => $node) {
            // new node
            $node = new Parser($node);

            // loop through
            foreach($node->findAll('javascript:void(0)', 2) as $j => $n) {
                $n = new Parser($n);

                $data = [
                    'name' => str_ireplace('>', null, $n->find('ic_reflection_box')->attr('title')),
                    'icon' => explode('?', $n->find('ic_reflection_box', 1)->attr('src'))[0],
                ];

                if ($i == 0) {
                    $character->mounts[] = $data;
                } else {
                    $character->minions[] = $data;
                }
            }
        }

        // unset parser
        unset($p);

        // dust up
        $character->clean();

        return $character;
    }

    /**
     * Parse character data, does it using regex, Faster
     *
     * @param $characterId - character id
     * @return Array - character data
     */
    private function advancedCharacterSearch($characterId)
    {
        // New character object
        $character = new Character();

        // Generate url and get html
        $url = $this->urlGen('characterProfile', [ '{id}' => $characterId ]);
        $rawHtml = $this->trim($this->curl($url), '<!-- contents -->', '<!-- //Minion -->');
		if($rawHtml == "<!-- contents -->" || $rawHtml == ""){
			return false;
		}

        $html = html_entity_decode(preg_replace(array('#\s\s+#s','#<script.*?>.*?</script>?#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);
        unset($rawHtml);

        # Namesection
        $nameHtml = $this->trim($html, '<!-- playname -->', '<!-- //playname -->');
		$this->_parseName($character,$nameHtml);

        # Profilesection
        $profileHtml = $this->trim($html, 'txt_selfintroduction', 'param_img_cover');
		$this->_parseProfile($character,$profileHtml);
		unset($profileHtml);

		# Class/Jobs
		$jobHtml = $this->trim($html, '<h4 class="class_fighter">', 'minion_box');
		$this->_parseJobs($character, $jobHtml);
		unset($jobHtml);

		# Gear
		$gearHtml = $this->trim($html, 'param_class_info_area', 'chara_content_title mb10');
		$this->_parseGear($character, $gearHtml);
		unset($gearHtml);

		# Attributes
		$attrHtml = $this->trim($html, 'param_left_area', 'class_fighter');
		$this->_parseAttributes($character, $attrHtml);
		unset($attrHtml);

		# Minions and Mounts
		$mountHtml = $this->trim($html, '<!-- Mount -->', '<!-- //Mount -->');

		$this->_parseMandM($character,$mountHtml,'mounts');
		unset($mountHtml);

		$minionHtml = $this->trim($html, '<!-- Minion -->', '<!-- //Minion -->');
		$this->_parseMandM($character,$minionHtml,'minions');
		unset($minionHtml);


		unset($html);

		// dust up
		$character->clean();

		return $character;
    }
	
	private function _parseName(Character &$character,$nameHtml){
		$nameMatches = array();
        // Build Namesectionpattern
        $namePattern = '<a href=".*?/(?<id>\d+)/">(?<name>[^<]+?)</a>';
        $worldPattern = '<span>\s?\((?<world>[^<]+?)\)\s?</span>';
        $titlePattern = '<div class="chara_title">(?<title>[^<]+?)</div>';
        $avatarPattern = '<div class="player_name_thumb"><a.+?>' . $this->getRegExp('image','avatar') . '</a></div>';
		
        // Build complete Expression and use condition to identify if title is before or after name
        $nameRegExp = sprintf('#(?J:%4$s<h2>(?:(?=<div)(?:%1$s)?%2$s%3$s|%2$s%3$s(?:%1$s)?)</h2>)#',$titlePattern,$namePattern,$worldPattern,$avatarPattern);
		
        if(preg_match($nameRegExp, $nameHtml, $nameMatches)){
            $character->id = $nameMatches['id'];
            $character->name = $nameMatches['name'];
            $character->title = $nameMatches['title'];
            $character->world = $nameMatches['world'];
            $character->avatar = $nameMatches['avatar'];
        }
		
		unset($nameHtml);
		unset($nameMatches);
		unset($nameRegExp);
		
		return $character;
	}
	
	private function _parseProfile(Character &$character,$profileHtml){
		$profileMatches = array();
        $profileRegExp = "#txt_selfintroduction\">(?<bio>.*?)</div>.*?"
                . "chara_profile_title\">(?<race>.*?)\s/\s(?<clan>.*?)\s/\s(?<gender>.*?)</div>.*?"
                . "icon.*?" . $this->getRegExp('image','guardianIcon') . ".*?"
                . "txt_name\">(?<nameday>.*?)</dd>.*?"
                . "txt_name\">(?<guardian>.*?)</dd>.*?"
                . "icon.*?" . $this->getRegExp('image','cityIcon') . ".*?"
                . "txt_name\">(?<city>.*?)</dd></dl>.*?"
                . "(?(?=<dl).*?<dt class=\"icon\">" . $this->getRegExp('image','grandCompanyIcon') . "</dt>.*?"
                . "txt_name\">(?<grandCompany>.*?)/(?<grandCompanyRank>.*?)</dd></dl>).*?"
                . "(?(?=<dl).*?<div class=\"ic_crest_32\"><span>" . $this->getRegExp('image','freeCompanyIcon1') . $this->getRegExp('image','freeCompanyIcon2') . $this->getRegExp('image','freeCompanyIcon3') . "</span></div>.*?"
                . "<dd class=\"txt_name\"><a href=\".*?/(?<freeCompanyId>\d+?)/\" class=\"txt_yellow\">(?<freeCompany>.*?)</a></dd>).*?"
                . "class=\"level\".*?(?<activeLevel>[\d]{1,2})</.*?"
                . "bg_chara_264.*?" . $this->getRegExp('image','portrait') . ""
                . "#u";
		
		if(preg_match($profileRegExp, $profileHtml, $profileMatches)){
			$character->freeCompanyIcon = [
				$profileMatches['freeCompanyIcon1'],
				$profileMatches['freeCompanyIcon2'],
				$profileMatches['freeCompanyIcon3']
			];
			foreach($profileMatches as $key => $value){
				if(!is_numeric($key) && property_exists($character, $key)){
					$character->$key = $value;
				}
			}
		}

		$character->avatarLarge = str_ireplace('50x50', '96x96', $character->avatar);
		$character->avatarMedium = str_ireplace('50x50', '64x64', $character->avatar);
		$character->portraitLarge = str_ireplace('264x360', '640x873', $character->portrait);
		
		unset($profileHtml);
		unset($profileMatches);
		unset($profileRegExp);
		
		return $character;
	}
	
	private function _parseJobs(Character &$character,$jobHtml){
		$jobMatches = array();
		$jobRegExp = '#ic_class_wh24_box.*?'
				. $this->getRegExp('image','icon') 
				. '(?<name>[^<]+?)</td><td[^>]*?>(?<level>[\d-]+?)</td>'
				. '<td[^>]*?>(?<exp_current>[\d-]+?)\s/\s(?<exp_level>[\d-]+?)</td'
				. '#';

		preg_match_all($jobRegExp, $jobHtml, $jobMatches, PREG_SET_ORDER);
		foreach($jobMatches as $match) {
			$this->clearRegExpArray($match);
			$character->classjobs[] = $match;
		}
		
		unset($jobHtml);
		unset($jobMatches);
		unset($jobRegExp);
		
		return $character;
	}
	
	private function _parseAttributes(Character &$character,$attrHtml){
		$attrMatches = array();
		$attrRegExp = "#li class=\"(?<attr>.*?)(?:\s?clearfix)?\">(?<content>.*?)</li#";

		preg_match_all($attrRegExp, $attrHtml, $attrMatches, PREG_SET_ORDER);

		foreach($attrMatches as $match) {
			array_shift($match);
			$key = strtolower(str_ireplace(' ', '-', $match['attr']));
			$value = $match['content'];
			if($match['attr'] == "") {
				preg_match('#<span class="left">(?<key>.*?)</span><span class="right">(?<value>.*?)</span>#', $match['content'], $tmpMatch);
				if(!array_key_exists('key', $tmpMatch))
					continue;
				$key = strtolower(str_ireplace(' ', '-', $tmpMatch['key']));
				$value = $tmpMatch['value'];
			}elseif(stripos($match['content'], 'val') !== false) {
				preg_match('#>(?<value>[\d-]*?)</span>#', $match['content'], $tmpMatch);
				$value = $tmpMatch['value'];
			}
			$character->attributes[$key] = intval($value);
		}
		$baseAttrRegExp = '#param_power_area.*?'
				. 'class="hp">(?<hp>[\d]+)<.*?'
				. '(?:class="mp">(?<mp>[\d]+)<.*?)?'
				. '(?:class="cp">(?<cp>[\d]+)<.*?)?'
				. '(?:class="gp">(?<gp>[\d]+)<.*?)?'
				. 'class="tp">(?<tp>[\d]+)<.*?'
				. '#';
		$baseAttrMatches = array();
		if(preg_match($baseAttrRegExp, $attrHtml, $baseAttrMatches)){
			$this->clearRegExpArray($baseAttrMatches);
			$character->attributes['hp'] = intval($baseAttrMatches['hp']);
			$character->attributes['mp'] = intval($baseAttrMatches['mp']);
			$character->attributes['cp'] = intval($baseAttrMatches['cp']);
			$character->attributes['gp'] = intval($baseAttrMatches['gp']);
			$character->attributes['tp'] = intval($baseAttrMatches['tp']);
		}
		
		unset($attrHtml);
		unset($attrRegExp);
		unset($attrMatches);
		unset($baseAttrMatches);
		
		return $character;
	}
	
	private function _parseMandM(Character &$character,$mandmHtml,$type){
		if($type != "minions" && $type != "mounts"){
			return false;
		}
		$mandmMatches = array();
		$mandmRegExp = "#<a.*?title=\"(?<name>.*?)\".*?" . $this->getRegExp('image','icon') . "#";

		preg_match_all($mandmRegExp, $mandmHtml, $mandmMatches, PREG_SET_ORDER);
		foreach($mandmMatches as $match) {
			$this->clearRegExpArray($match);
			$character->{$type}[] = $match;
		}
		unset($mandmHtml);
		unset($mandmRegExp);
		unset($mandmMatches);
		return $character;
	}
	
	private function _parseGear(Character &$character,$gearHtml){
		$itemsMatch = array();
		$gearRegExp = '#<!-- ITEM Detail -->.*?'
				. '<div class="name_area[^>].*?>.*?'
				. '(?:<div class="(?<mirage>mirage)_staining (?<mirageType>unpaitable|painted_cover|no_paint)"(?: style="background\-color:\s?(?<miragePaintColor>\#[a-fA-F0-9]{6});")?></div>)?'
				. '<img[^>]+?>' . $this->getRegExp('image','icon') . '.*?'
				. '<div class="item_name_right">'
				. '<div class="item_element[^"]*?">'
				. '<span class="rare">(?<rare>[^<]*?)</span>'
				. '<span class="ex_bind">\s*(?<binding>[^<]*?)\s*</span></div>'
				. '<h2 class="item_name\s?(?<color>[^_]*?)_item">(?<name>[^<]+?)(?<hq><img.*?>)?</h2>.*?'
				// Glamoured?
				. '(?(?=<div)(<div class="mirageitem.*?">)'
				. '<div class="mirageitem_ic">' . $this->getRegExp('image','mirageItemIcon') . '.*?'
				. '<p>(?<mirageItemName>[^<]+?)<a.*?href="/lodestone/playguide/db/item/(?<mirageItemId>[\w\d^/]+)/".*?></a></p>'
				. '</div>)'
				//
				. '<h3 class="category_name">(?<slot>[^<]*?)</h3>.*?'
				. '<a href="/lodestone/playguide/db/item/(?<id>[\w\d]+?)/".*?>.*?</a></div>'
				. '(?(?=<div class="popup_w412_body_inner mb10">).*?'
				. '<div class="parameter\s?.*?"><strong>(?<parameter1>[^<]+?)</strong></div>'
				. '<div class="parameter"><strong>(?<parameter2>[^<]+?)</strong></div>'
				. '(?:<div class="parameter"><strong>(?<parameter3>[^<]+?)</strong></div>)?'
				. '</div>)'
				. '.*?<div class="pt3 pb3">.+?\s(?<ilv>[0-9]{1,3})</div>.*?'
				. '<span class="class_ok">(?<classes>[^<]*?)</span><br>'
				. '<span class="gear_level">[^\d]*?(?<gearlevel>[\d]+?)</span>.*?'
				. '(?:<ul class="basic_bonus">(?<bonuses>.*?)</ul>)?.*?'
				. '<li class="clearfix".*?><div>(?<durability>.*?)%</div></li>'
				. '<li class="clearfix".*?><div>(?<spiritbond>.*?)%</div></li>'
				. '<li class="clearfix".*?><div>(?<repairClass>[\w]+?)\s[\w\.]+?\s(?<repairLevel>\d*?)</div></li>'
				. '<li class="clearfix".*?><div>(?<materials>.*?)<\/div><\/li>.*?'
				/** @TODO mutlilanguage **/
				. '<ul class="ml12"><li>[\s\w]+?:\s(?<convertible>Yes|No)[\s\w]+?:\s(?<projectable>Yes|No)[\s\w]+?:\s(?<desynthesizable>Yes|No)[\s\w]*?<\/li><\/ul>.*?'
				. '<span class="sys_nq_element">(?<sellable>.*?)</span>'
				. '.*?<!-- //ITEM Detail -->#u';
		
		preg_match_all($gearRegExp, $gearHtml, $itemsMatch, PREG_SET_ORDER);


		$i = 0;
		$iLevelTotal = 0;
		$iLevelArray = [];
		$bonusRegExp = '#<li>(?<type>.*?)\s\+?(?<value>\-?\d+)</li>#i';
		foreach($itemsMatch as $match) {
			$this->clearRegExpArray($match);
			// Basestats
			if($match['slot'] == 'Shield'){ // Shield
				$match['block_strength'] = $match['parameter1'];
				$match['block_rate'] = $match['parameter2'];
			}else if($match['parameter3'] == ""){ // Normalitem
				$match['defense'] = $match['parameter1'];
				$match['magical_defense'] = $match['parameter2'];
			}else{ // Weapon
				$match['damage'] = $match['parameter1'];
				$match['auto_attack'] = $match['parameter2'];
				$match['delay'] = $match['parameter3'];
			}
			unset($match['parameter1']);
			unset($match['parameter2']);
			unset($match['parameter3']);
			// HighQualityItem
			$match['hq'] = ($match['hq'] == "") ? false : true;
			//Bonuses
			$bonusMatch = array();
			preg_match_all($bonusRegExp,$match['bonuses'],$bonusMatch, PREG_SET_ORDER);
			$match['bonuses'] = $this->clearRegExpArray($bonusMatch);
			
			$character->gear[] = $match;

			if ($match['slot'] != 'Soul Crystal') {
				$iLevelTotal = $iLevelTotal + $match['ilv'];
				$iLevelArray[] = $match['ilv'];

				if (in_array($match['slot'], $this->getTwoHandedItems())) {
					$iLevelArray[] = $match['ilv'];
				}
			}

			// active job
			// TODO multilanguage
			if ($match['slot'] == 'Soul Crystal') {
				$character->activeJob = str_ireplace('Soul of the ', null, $match['name']);
			}
			$i++;
		}

		// active class
		$activeClassMatch= array();
		$possibleClasses = array();
		foreach($character->classjobs as $job){
			$possibleClasses[] = $job['name'];
		}
		if (preg_match('#('. implode('?|',$possibleClasses).')#i',$itemsMatch[0]['slot'],$activeClassMatch) === 1) {
			$character->activeClass = $activeClassMatch[1];
		}

		$character->gearStats = [
			'total' => $iLevelTotal,
			'average' => floor(array_sum($iLevelArray) / 13),
			'array' => $iLevelArray,
		];
		//Unsets
		unset($gearHtml);
		unset($gearRegExp);
		unset($itemsMatch);
		unset($activeClassMatch);
		unset($possibleClasses);
		unset($iLevelArray);

		return $character;
			
	}

    /**
     * Get achievements for a character
     *
     * @param $characterId - the id of the character
     * @param $all - all achievements or summary?
     * @return Achievement - an achievement object
     */
    public function Achievements($characterId, $all = false)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($characterId)) {

            // If basic searching
            if ($this->basicParsing) {
                return $this->basicAchievementsParse($characterId, $all);
            }

            // Advanced searching
            return $this->advancedAchievementsParse($characterId, $all);
        }
    }

    /**
     * Parse achievement data, does it using basic methods, Slower.
     *
     * @param $characterId - the character id
     * @param $all - all achievements or summary?
     * @return Achievement - an achievement object
     */
    private function basicAchievementsParse($characterId, $all)
    {
        if ($all)
        {
            // If legacy or not.
            $isLegacy = false;

            // get kinds
            $kinds = $this->getAchievementKinds();

            // New character object
            $achievement = new Achievements();

            // loop through kinds
            foreach($kinds as $kind => $type)
            {
                // Skip if this is the legacy kind and character is not legacy
                if ($kind == 13 && !$isLegacy) {
                    continue;
                }

                // Generate url
                $url = $this->urlGen('achievementsKind', [ '{id}' => $characterId, '{kind}' => $kind ]);
                $html = $this->trim($this->curl($url), '<!-- tab menu -->', '<!-- //base -->');

                // get doc
                $p = new Parser($html);

                // Begin parsing/populating character
                $achievement->pointsCurrent = $p->find('txt_yellow')->numbers();
                $achievement->legacy = (strlen($p->find('legacy')->html()) > 0) ? true : false;
                $isLegacy = $achievement->legacy;

                if ($achievement->pointsCurrent) {
                    $achievement->public = true;
                } else {
                    // end, not public
                    break;
                }

                foreach($p->findAll('ic_achievement', 'button bt_more') as $i => $node)
                {
                    $node = new Parser($node);

                    $id         = explode('/', $node->find('bt_more')->attr('href'))[6];
                    $points     = $node->find('achievement_point')->numbers();
                    $time       = $this->extractTime($node->find('getElementById')->html());
                    $obtained   = ($time) ? true : false;

                    $achievement->list[$id] =
                    [
                        'id'        => $id,
                        'icon'      => explode('?', $node->find('ic_achievement', ($kind == 13 ? 1 : 2))->attr('src'))[0],
                        'name'      => $node->find('achievement_name')->text(),
                        'time'      => $time,
                        'obtained'  => $obtained,
                        'points'    => $points,
                        'kind'      => $type,
                        'kind_id'   => $kind,
                    ];

                    // prevent php notices
                    if (!isset($achievement->kinds[$type])) { $achievement->kinds[$type] = 0; }
                    if (!isset($achievement->kindsTotal[$type])) { $achievement->kindsTotal[$type] = 0; }

                    // Increment kinds
                    $achievement->kindsTotal[$type] = $achievement->kindsTotal[$type] + $points;
                    if ($obtained) {
                        $achievement->kinds[$type] += $points;
                        $achievement->countCurrent += 1;
                    }

                    // Increment overall total
                    $achievement->pointsTotal += $points;
                    $achievement->countTotal += 1;

                    if ($kind == 13) {
                        $achievement->legacyPointsTotal += $points;
                        if ($obtained) {
                            $achievement->legacyPoints += $points;
                        }
                    }
                }
            }
        }
        else
        {
            // New character object
            $achievement = new Achievements();

            // Setup url and get html
            $url = $this->urlGen('achievements', [ '{id}' => $characterId ]);
            $html = $this->trim($this->curl($url), '<!-- tab menu -->', '<!-- //base -->');

            $p = new Parser($html);

            // Begin parsing/populating character
            $achievement->pointsCurrent = $p->find('txt_yellow')->numbers();
            $achievement->legacy = (strlen($p->find('legacy')->html()) > 0) ? true : false;

            if ($achievement->pointsCurrent) {
                $achievement->public = true;
            }

            // Recent
            foreach($p->findAll('ic_achievement', 'achievement_area_footer') as $i => $node)
            {
                $node = new Parser($node);
                $id = explode('/', $node->find('ic_achievement', 1)->html())[6];

                $achievement->recent[$id] =
                [
                    'id'   => $id,
                    'icon' => explode('?', $node->find('ic_achievement', 3)->attr('src'))[0],
                    'name' => $node->find('achievement_date', 4)->text(),
                    'time' => $this->extractTime($node->find('getElementById', 0)->html()),
                ];
            }
        }

        // Dust up
        $achievement->clean();

        // return
        return $achievement;
    }

    /**
     * Parse achievement data, does it using regex, Faster
     *
     * @param $characterId - the character id
     * @param $all - all achievements or summary?
     * @return Achievement - an achievement object
     */
    public function advancedAchievementsParse($id, $all)
    {
        // If legacy or not.
        $isLegacy = false;

        // get kinds
        $kinds = $this->getAchievementKinds();

        // New character object
        $achievement = new Achievements();

        // loop through kinds
        foreach($kinds as $kind => $type) {
            // Skip if this is the legacy kind and character is not legacy
            if ($kind == 13 && !$isLegacy) {
                continue;
            }

            // Generate url
            $url = $this->urlGen('achievementsKind', [ '{id}' => $id, '{kind}' => $kind ]);
            $rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
            $html = html_entity_decode(preg_replace(array('#\s\s+#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);

            $achievementMatch = array();
            preg_match('#class="txt_yellow">(?<pointsCurrent>\d+)</strong>.*?(?<legacy>(?<=.)legacy|\#main)#',$html,$achievementMatch);
            $achievement->pointsCurrent = $achievementMatch['pointsCurrent'];
            $achievement->legacy = ($achievementMatch['legacy'] == "legacy");
            $isLegacy = $achievement->legacy;
            # Achievments
            $regExp = "#<li><div class=\"(?<achieved>.*?)\">.*?" . $this->getRegExp('image','icon') . ".*?achievement_name.*?>(?<name>.*?)</span>(?<dateHTML>.*?)achievement_point.*?>(?<points>[\d]+)</div>.*?<a.*?href=\"/lodestone/character/[\d]+/achievement/detail/(?<id>[\d]+)/\".*?</li>#";

            $achievmentMatches = array();
            preg_match_all($regExp, $html, $achievmentMatches, PREG_SET_ORDER);
            foreach($achievmentMatches as $mkey => $match) {
                $obtained = $match['achieved'] == "already" ? true : false;
                if($match['achieved'] == "already"){
                    preg_match('#ldst_strftime\(([\d\.]+),#',$match['dateHTML'],$dateMatch);
                    $time = filter_var($dateMatch[1], FILTER_SANITIZE_NUMBER_INT);
                }else{
                    $time = null;
                }
                $points = filter_var($match['points'], FILTER_SANITIZE_NUMBER_INT);
                $achievement->list[$match['id']] =
                [
                    'id' => $match['id'],
                    'icon' => $match['icon'],
                    'iconTimestanp' => $match['iconTimestamp'],
                    'name' => $match['name'],
                    'time' => $time,
                    'obtained' =>$obtained,
                    'points' =>  $match['points'],
                    'kind' => $type,
                    'kind_id' => $kind,
                ];

                // prevent php notices
                if (!isset($achievement->kinds[$type])) { $achievement->kinds[$type] = 0; }
                if (!isset($achievement->kindsTotal[$type])) { $achievement->kindsTotal[$type] = 0; }

                // Increment kinds
                $achievement->kindsTotal[$type] = $achievement->kindsTotal[$type] + $points;
                if ($obtained) {
                    $achievement->kinds[$type] = $achievement->kinds[$type] + $points;
                    $achievement->countCurrent = $achievement->countCurrent + 1;
                }

                // Increment overall total
                $achievement->pointsTotal = $achievement->pointsTotal + $points;
                $achievement->countTotal = $achievement->countTotal + 1;
            }
        }

        // Dust up
        $achievement->clean();

        // return
        return $achievement;
    }

    /**
     * Get onlinestatus of servers
     * @return array
     */
    public function Worldstatus($datacenter=null,$server=null) {
        $worldStatus = array();

		// Set server null if datacenter null to avoid errors
		if(is_null($datacenter) || $server == ""){
			$server = null;
		}

        // Generate url
        $url = $this->urlGen('worldstatus', []);
        $rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
        $html = html_entity_decode(preg_replace(array('#\s\s+#s','#<script.*?>.*?</script>?#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);
		$datacenterMatches = array();
		$datacenterRegExp = is_null($datacenter) ? ".*?" : $datacenter;
		$regExp = '#text-headline.*?</span>(?<datacenter>'.$datacenterRegExp.')</div>.*?(?<tableHTML><table.*?</table>)#';
		preg_match_all($regExp, $html, $datacenterMatches, PREG_SET_ORDER);
		foreach($datacenterMatches as $key => $data){
			$serverStatus = $this->_parseServerstatus($data['tableHTML'],$server);
			$worldStatus[$data['datacenter']] = $serverStatus;
		}
		if(!is_null($datacenter)){
			$return = array_shift($worldStatus);
			if(!is_null($server)){
				return $return[0]['status'];
			}
			return $return;
		}
        return $worldStatus;
    }

	private function _parseServerstatus($datacenterTableHTML,$server=null){
		$serverMatches = array();
		$serverRegExp = is_null($server) ? '\w+?' : $server;
		$regExp = '#relative">(?<server>'.$serverRegExp.')</div>.*?ic_worldstatus_1">(?<status>[\w\s]+)</span>#';
		preg_match_all($regExp, $datacenterTableHTML, $serverMatches, PREG_SET_ORDER);
		$this->clearRegExpArray($serverMatches);
		return $serverMatches;
	}

    /**
     * get topics
     */
    public function Topics($hash=null){
        if(is_null($hash)){
            return $this->_newsParser('topics');
        }else{
            return $this->_newsDetailParser('topics',$hash);
		}
    }

    /**
     * get notices
     */
    public function Notices($hash=null){
        if(is_null($hash)){
            return $this->_newsParser('notices');
        }else{
            return $this->_newsDetailParser('notices',$hash);
		}
    }

    /**
     * get maintenance
     */
    public function Maintenance($hash=null){
        if(is_null($hash)){
            return $this->_newsParser('maintenance');
        }else{
            return $this->_newsDetailParser('maintenance',$hash);
		}
    }

    /**
     * get updates
     */
    public function Updates($hash=null){
        if(is_null($hash)){
            return $this->_newsParser('updates');
        }else{
            return $this->_newsDetailParser('updates',$hash);
		}
    }

    /**
     * get status
     */
    public function Status($hash=null){
        if(is_null($hash)){
            return $this->_newsParser('status');
        }else{
            return $this->_newsDetailParser('status',$hash);
		}
    }

    /**
     * get news
     */
    private function _newsParser($type){
        $matches = array();
		// Generate url
		$url = $this->urlGen($type, []);

		// Special Regexp for topics-section
		if($type == 'topics'){
			$rawHtml = $this->trim($this->curl($url), '<!-- topics -->', '<!-- //topics -->');
			$regExp = '#<li class="clearfix">.*?'
					. '<script>.*?ldst_strftime\((?<date>[\d]+?),.*?'
					. '<a href="/lodestone/topics/detail/(?<linkHash>[\w\d]+)">(?<headline>.*?)</a>.*?'
					. '<div class="area_inner_cont">'
					. '<a.*?>' . $this->getRegExp('image','teaser') . '</a>'
					. '(?<bodyHTML>.*?)'
					. '</div><div class="right_cont.*?'
					. '</li>#';
		}else{
			$rawHtml = $this->trim($this->curl($url), '<!-- news -->', '<!-- pager -->');
			$regExp = '#<dl.*?'
					. '<script>.*?ldst_strftime\((?<date>[\d]+?),.*?'
					. '(?:<span class="tag">\[(?<type>.*?)\]</span>)?'
					. '<a href="/lodestone/news/detail/(?<linkHash>[\w\d]+)".*?>(?<body>.*?)</a>.*?'
					. '</dl>#';
		}
		$html = html_entity_decode(preg_replace(array('#\s\s+#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);
		preg_match_all($regExp, $html, $matches, PREG_SET_ORDER);
		$this->clearRegExpArray($matches);
		return $matches;
    }

    /**
     * get news
     */
    private function _newsDetailParser($type,$hash){
        $match = array();
		// Generate url
		$urlType = $type == 'topics' ? 'topicsDetail' : 'newsDetail';
		$url = $this->urlGen($urlType, ['{hash}' => $hash]);

		$rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
		$imgRegexp = ($type == 'topics') ? '<center>' . $this->getRegExp('image','teaser') . '</center><br>' : '';

		$regExp = '#<script>.*?ldst_strftime\((?<date>[\d]+?),.*?'
				. '<div class="topics_detail_txt">(?:<span class="topics_detail_tag">\[(?<type>.*?)\]</span>)?(?<headline>.*?)</div>.*?'
				. '<div.*?>'.$imgRegexp.'(?<body>.*?)</div>'
				. '(?:</div></div></div><div class="(?:diary_nav|area_body)|<!-- social buttons -->)#';

		$html = html_entity_decode(preg_replace(array('#\s\s+#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);
		preg_match($regExp, $html, $match);
		$this->clearRegExpArray($match);
		return $match;
    }

    /**
     * Get freecompany
     *
     * @param $freeCompanyId - the id of the freecompany
     * @param $members - with memberlist
     */
    public function advancedFreecompanyParse($freeCompanyId, $members = false) {

        // Generate url
        $url = $this->urlGen('freecompany', ['{id}' => $freeCompanyId]);
        $rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
        $html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

        $freeCompany = new \stdClass();
        $headerHtml = $this->trim($html, '<!-- playname -->', '<!-- //playname -->');

		$freeCompany->id = $freeCompanyId;
        $headerRegExp = '#' . $this->getRegExp('image','fcIcon1') . '.*?'
                        . $this->getRegExp('image','fcIcon2') . '.*?'
                        . $this->getRegExp('image','fcIcon3') . '.*?'
                        . '.*?crest_id.*?>(?<company>.*?)\s?<.*?<span>\s?\((?<world>.+?)\)</span>#';
        $headerMatches = array();
        if(preg_match($headerRegExp, $headerHtml, $headerMatches)) {
            $freeCompany->emblum = array(
                $headerMatches['fcIcon1'],
                $headerMatches['fcIcon2'],
                $headerMatches['fcIcon3'],
                );
            $freeCompany->company = $headerMatches['company'];
            $freeCompany->server = $headerMatches['world'];

        }

        $baseHtml = $this->trim($html, '<!-- Company Profile -->', '<!-- //Company Profile -->');

        $regExp = '#<td class="vm"><span class="txt_yellow">(?<name>.*?)</span><br>«(?<tag>.*?)»</td>.*?'
                . 'ldst_strftime\((?<formed>[\d\.]+),.*?'
                . '<td>(?<activeMember>[\d]+)</td>.*?'
                . '<td>(?<rank>[\d]+)</td>.*?'
                // Weekly&Monthly
                . '</th><td>.*?:\s*(?<weeklyRank>[\d\-]+)\s*.*?<br>.*?:\s*(?<monthlyRank>[\d\-]+).*?</td>.*?'
                . '</th><td>(?<slogan>.*?)</td>.*?'
                // Skip Focus && Seeking
                . '<tr>.*?</tr><tr>.*?</tr>.*?'
                //
                . '<td>(?!<ul>)(?<active>.*?)</td>.*?'
                . '<td>(?<recruitment>.*?)</td>.*?'
                // Estate
                . '<td>'
				. '(?(?=<div)'
				. '<div class="txt_yellow.*?">(?<estateZone>.*?)</div>.*?'
                . '<p class="mb10.*?">(?<estateAddress>.*?)</p>.*?'
                . '<p class="mb10.*?">(?<estateGreeting>.*?)</p>'
				. ').*?</td>.*?'
                . '#';
        $matches = array();
        if(preg_match($regExp, $baseHtml, $matches)) {
            $freeCompany->name = $matches['name'];
            $freeCompany->tag = $matches['tag'];
            $freeCompany->formed = $matches['formed'];
            $freeCompany->id = $freeCompanyId;
            $freeCompany->memberCount = $matches['activeMember'];
            $freeCompany->slogan = $matches['slogan'];
            $freeCompany->active = $matches['active'];
            $freeCompany->recruitment = $matches['recruitment'];
            $freeCompany->ranking = array(
                'current' => $matches['rank'],
                'weekly' => $matches['weeklyRank'],
                'monthly' => $matches['monthlyRank'],
            );
			if(array_key_exists('estateZone', $matches)){
				$freeCompany->estate = array(
					'zone' => $matches['estateZone'],
					'address' => $matches['estateAddress'],
					'message' => $matches['estateGreeting'],
				);
			}
        }
        // Focus & Seeking
        $regExp = '#<li(?: class="icon_(?<active>off?)")?><img src="(?<icon>.*?/ic/(?<type>focus|roles)/.*?)\?.*?title="(?<name>.*?)">#';
        $FocusOrSeekingMatches = array();
        preg_match_all($regExp, $baseHtml, $FocusOrSeekingMatches, PREG_SET_ORDER);
        foreach($FocusOrSeekingMatches as $key => $match){
            $freeCompany->{$match['type']}[] = [
                'name' => $match['name'],
                'icon' => $match['icon'],
                'active' => $match['active'] != "" ? false : true,
            ];
        }

        if($members === true){
            $freeCompany->members = array();
            $url = $this->urlGen('freecompanyMember', ['{id}' => $freeCompany->id]);
            $rawHtml = $this->trim($this->curl($url), '<!-- Member List -->', '<!-- //Member List -->');
            $html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s','#<script.*?>.*?</script>?#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

            $maxPerPage = strip_tags($this->trim($html,'<span class="show_end">','</span>'));
            $pages = ceil($freeCompany->memberCount/$maxPerPage);
			$memberHtml = "";
            for($page = 1;$page<=$pages;$page++){
                if($page == 1){
                    $memberHtml .= $this->trim($html, 'table_black_border_bottom', '<!-- pager -->');
                }else{
                    $pageUrl = $this->urlGen('freecompanyMemberPage', ['{id}' => $freeCompanyId, '{page}' => $page]);
                    $rawPageHtml = $this->trim($this->curl($pageUrl), '<!-- Member List -->', '<!-- //Member List -->');
                    $pageHtml = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s','#<script.*?>.*?</script>?#s', '#<!--\s*-->#s'), '', $rawPageHtml), ENT_QUOTES);
                    $memberHtml .= $this->trim($pageHtml, 'table_black_border_bottom', '<!-- pager -->');
                }
            }
            $freeCompany->members = $this->_advancedFcMemberParse($memberHtml);
        }

        return $freeCompany;
    }

    private function _advancedFcMemberParse($html){
        $regExp = '#<tr\s?>.*?<a href="/lodestone/character/(?<id>\d+)/">'
                . $this->getRegExp('image','avatar') . '.*?'
                . '<a .*?>(?<name>.+?)</a><span>\s?\((?<world>.+?)\)</span>.*?'
                . '<div class="fc_member_status">' . $this->getRegExp('image','rankIcon') . '(?<rankName>.+?)</div>.*?'
                . '<div class="ic_box">' . $this->getRegExp('image','classIcon') . '</div>'
                . '<div class="lv_class">(?<classLevel>\d+?)</div></div>'
                . '(?:<div class="ic_gc"><div>' . $this->getRegExp('image','gcIcon') . '</div>'
                . '<div>(?<gcName>[^/]+?)/(?<gcRank>[^/]+?)</div>)?.*?</tr>#';
        $memberMatch= array();
        preg_match_all($regExp, $html, $memberMatch, PREG_SET_ORDER);
        $members = array();
        foreach($memberMatch as $key => $member){
            $members[$key] = array(
                'avatar' => $member['avatar'],
                'name' => $member['name'],
                'id' => $member['id'],
                'rank' => array(
                    'title' => $member['rankName'],
                    'image' => $member['rankIcon'],
                    ),
                'class' => array(
                    'image' => $member['classIcon'],
                    'level' => $member['classLevel']
                )
            );
            if(array_key_exists('gcIcon', $member)){
                $members[$key]['grandcompany'] = array(
                    'image' => $member['gcIcon'],
                    'name' => $member['gcName'],
                    'rank' => $member['gcRank']
                );
            }
        }
        return $members;
    }

    /**
     * Get linkshell
     *
     * @param $linkshellId - the id of the freecompany
     */
    public function advancedLinkshellParse($linkshellId) {

        // Generate url
        $url = $this->urlGen('linkshell', ['{id}' => $linkshellId]);
        $rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
        $html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

        $linkshell = new \stdClass();

        $headerHtml = $this->trim($html, '<!-- playname -->', '<!-- narrowdown -->');

        $headerRegExp = '#<h2.*?>(?<name>.*?)<span>\s?\((?<world>.+?)\)</span></h2>.*?<h3 class="ic_silver">.*?\((?<memberCount>\d+).*?</h3>#';
        $headerMatches = array();
        if(preg_match($headerRegExp, $headerHtml, $headerMatches)) {
            $linkshell->id = $linkshellId;
            $linkshell->name = $headerMatches['name'];
            $linkshell->server = $headerMatches['world'];
            $linkshell->memberCount = $headerMatches['memberCount'];

        }

        $linkshell->members = array();
        $url = $this->urlGen('linkshellPage', ['{id}' => $linkshell->id]);
        $rawHtml = $this->trim($this->curl($url), '<!-- base_inner -->', '<!-- //base_inner -->');
        $html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s','#<script.*?>.*?</script>?#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

        $maxPerPage = strip_tags($this->trim($html,'<span class="show_end">','</span>'));
        $pages = ceil($linkshell->memberCount/$maxPerPage);
        for($page = 1;$page<=$pages;$page++){
            if($page == 1){
                $memberHtml = $this->trim($html, 'table_black_border_bottom', '<!-- pager -->');
            }else{
                $pageUrl = $this->urlGen('linkshellPage', ['{id}' => $linkshell->id, '{page}' => $page]);
                $rawPageHtml = $this->trim($this->curl($pageUrl), '<!-- base_inner -->', '<!-- //base_inner -->');
                $pageHtml = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s','#<script.*?>.*?</script>?#s', '#<!--\s*-->#s'), '', $rawPageHtml), ENT_QUOTES);
                $memberHtml .= $this->trim($pageHtml, 'table_black_border_bottom', '<!-- pager -->');
            }
        }
        $linkshell->members = $this->_advancedLsMemberParse($memberHtml);


        return $linkshell;
    }

    private function _advancedLsMemberParse($html){
        $regExp = '#<tr\s?>.*?<a href="/lodestone/character/(?<id>\d+)/">'
                . $this->getRegExp('image','avatar') . '.*?'
                . '<a .*?>(?<name>.+?)</a><span>\s?\((?<world>.+?)\)</span>.*?'
                . '<div class="col3box">.*?' . $this->getRegExp('image','classIcon') . '</div>'
                . '<div>(?<classLevel>\d+?)</div></div>.*?'
                . '(?:(?<=<div class="col3box_center">)<div>' . $this->getRegExp('image','gcRankIcon') . '<div>(?<gcName>[^/]+?)/(?<gcRank>[^/]+?)</div>|</div>).*?'
                // fcData
                . '(?:(?<=<div class="ic_crest_32">)<span>' . $this->getRegExp('image','fcIcon1') . $this->getRegExp('image','fcIcon2') . '(?:' . $this->getRegExp('image','fcIcon3') . ')?</span></div></div><div class="txt_gc"><a href="/lodestone/freecompany/(?<fcId>\d+)/">(?<fcName>.*?)</a></div>|</td>).*?'
                . '</tr>#';

        $memberMatch= array();
        preg_match_all($regExp, $html, $memberMatch, PREG_SET_ORDER);
        $members = array();
        foreach($memberMatch as $key => $member){
            $members[$key] = array(
                'avatar' => $member['avatar'],
                'name' => $member['name'],
                'id' => $member['id'],
                'class' => array(
                    'image' => $member['classIcon'],
                    'level' => $member['classLevel']
                )
            );
            if(array_key_exists('gcRankIcon', $member)){
                $members[$key]['grandcompany'] = array(
                    'image' => $member['gcRankIcon'],
                    'name' => $member['gcName'],
                    'rank' => $member['gcRank']
                );
            }
            if(array_key_exists('fcName', $member)){
                $members[$key]['freecompany'] = array(
                    'image' => array(
                        $member['fcIcon1'],
                        $member['fcIcon2'],
                        $member['fcIcon3']
                        ),
                    'name' => $member['fcName'],
                    'id' => $member['fcId']
                );
            }
        }
        return $members;
    }

}