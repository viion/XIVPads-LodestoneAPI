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
        if (is_numeric($nameOrId)) {
            // If basic searching
            if ($this->basicParsing) {
                return $this->basicCharacterSearch($nameOrId);
            }

            // Advanced searching
            return $this->advancedCharacterSearch($nameOrId);

        } else {

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
        $character->grandCompanyRank = explode('/', $p->find('chara_profile_left',16)->text())[1];
        $character->grandCompanyIcon = $p->find('chara_profile_left', 14)->attr('src');
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
                    'exp_total' => intval($exp[1]),
                ];
            }

            unset($node);
        }

        # Gear

        $iLevelTotal = 0;
        $iLevelArray = [];
        foreach($p->findAll('item_detail_box', 35, 'param_power_area') as $i => $node) {
            // new node
            $node = new Parser($node);
            $ilv = filter_var($node->find('pt3 pb3')->text(), FILTER_SANITIZE_NUMBER_INT);
            $slot = $node->find('ex_bind', 5)->text();
            $name = str_ireplace('">', null, $node->find('ex_bind', 4)->text());

            $character->gear[] = [
                'icon'  => explode('?', $node->find('itemicon')->attr('src'))[0],
                'color' => explode('_', $node->find('ex_bind', 3)->text())[0],
                'name'  => $name,
                'slot'  => $slot,
                'id'    => explode('/', $node->find('bt_db_item_detai', 1)->html())[5],
                'ilv'   => $ilv ,
            ];

            if ($slot != 'Soul Crystal') {
                $iLevelTotal = $iLevelTotal + $ilv;
                $iLevelArray[] = $ilv;

                if (in_array($slot, $this->getTwoHandedItems())) {
                    $iLevelArray[] = $ilv;
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
            'average' => floor(array_sum($iLevelArray) / 13),
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
        $html = html_entity_decode(preg_replace(array('#\s\s+#s','#<script.*?>.*?</script>?#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);

        // Base Data
        $regExp = "#player_name_thumb.*?src=\"(?<avatar>.*?)\?.*?"
                . "<a.*?href=\"/lodestone/character/(?<id>[\w\d]+?)/\".*?>(?<name>.*?)</a>"
                . "<span>\s*?\((?<world>.*?)\).*?"
                . "<div class=\"chara_title\">(?<title>.*?)</div>.*?"
                . "txt_selfintroduction\">(?<bio>.*?)</div>.*?"
                . "chara_profile_title\">(?<race>.*?)\s/\s(?<clan>.*?)\s/\s(?<gender>.*?)</div>.*?"
                . "icon.*?img.*?src=\"(?<guardianIcon>.*?)\?.*?"
                . "txt_name\">(?<nameday>.*?)</dd>.*?"
                . "txt_name\">(?<guardian>.*?)</dd>.*?"
                . "icon.*?img.*?src=\"(?<cityIcon>.*?)\?.*?"
                . "txt_name\">(?<city>.*?)</dd>.*?"
                . "icon.*?img.*?src=\"(?<grandCompanyIcon>.*?)\?.*?"
                . "txt_name\">(?<grandCompany>.*?)/(?<grandCompanyRank>.*?)</dd>.*?"
                . "class=\"level\".*?(?<activeLevel>[\d]{1,2})</.*?"
                . "bg_chara_264.*?img.*?src=\"(?<portrait>.*?)\?"
                . "#";
			$matches = array();
			if(preg_match($regExp, $html, $matches)){
				foreach($matches as $key => $value){
					if(!is_numeric($key) && property_exists($character, $key)){
						$character->$key = $value;
					}
				}
			}

            $character->avatarLarge = str_ireplace('50x50', '96x96', $character->avatar);
            $character->portraitLarge = str_ireplace('264x360', '640x873', $character->portrait);

			$freeCompanyRegExp = "#ic_crest_32.*?src=\"(?<freecompanyIcon1>.*?)\".*?src=\"(?<freecompanyIcon2>.*?)\".*?src=\"(?<freecompanyIcon3>.*?)\".*?"
								. "txt_name\">.*?href=\".*?/(?<freecompanyid>[\d]+?)/\".*?>(?<freecompany>.*?)</a>.*?#";
			if(preg_match($freeCompanyRegExp, $html, $matches)){
				array_shift($matches);
				$character->freecompanyid = $matches['freecompanyid'];
				$character->freecompany = $matches['freecompany'];
				$character->freeCompanyIcon = [
					$matches['freecompanyIcon1'],
					$matches['freecompanyIcon2'],
					$matches['freecompanyIcon3']
				];
			}

            # Class/Jobs
			$possibleClasses = array();
			$jobHtml = $this->trim($html, '<h4 class="class_fighter">', 'minion_box');
			$regExp = "#ic_class_wh24_box.*?<img.*?src=\"(?<icon>.*?)\?.*?>(?<name>[^<]+?)</td><td[^>]*?>(?<level>[\d-]+?)</td><td[^>]*?>(?<exp_current>[\d-]+?)\s/\s(?<exp_total>[\d-]+?)</td#";

			preg_match_all($regExp, $jobHtml, $matches, PREG_SET_ORDER);
			foreach($matches as $mkey => $match) {
				$this->clearRegExpArray($match);
				$character->classjobs[] = $match;
				$possibleClasses[] = $match['name'];
			}

            # Gear
            $i = 0;
            $iLevelTotal = 0;
            $iLevelArray = [];
			$gearHtml = $this->trim($html, 'param_class_info_area', 'chara_content_title mb10');
			$regExp = "#item_detail_box.*?ic_reflection_box_64.*?<img.*?src=\"(?<icon>[^\"]+?itemicon[^\"]+)\?.*?<h2.*?class=\"item_name\s?(?<color>.*?)_item\".*?>(?<name>[^<]*?)</h2>.*?class=\"category_name\">(?<slot>[\w\-\s\']*?)</h3>.*?<a href=\"/lodestone/playguide/db/item/(?<id>[\w\d]+?)/\".*?class=\"pt3 pb3\">.+?\s(?<ilv>[0-9]{1,3})</div>#u";

			preg_match_all($regExp, $gearHtml, $matches, PREG_SET_ORDER);
			foreach($matches as $mkey => $match) {
				$this->clearRegExpArray($match);
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
			// TODO multilanguage
			$activeClassMatch= array();
			if (preg_match('#('. implode('?|',$possibleClasses).')#i',$matches[0]['slot'],$activeClassMatch) === 1) {
				$character->activeClass = $activeClassMatch[1];
			}

            $character->gearStats = [
                'total' => $iLevelTotal,
                'average' => floor(array_sum($iLevelArray) / 13),
                'array' => $iLevelArray,
            ];

            # Attributes
			$attrHtml = $this->trim($html, 'param_left_area', 'class_fighter');
			$regExp = "#li class=\"(?<attr>.*?)(?:\s?clearfix)?\">(?<content>.*?)</li#";

			preg_match_all($regExp, $attrHtml, $matches, PREG_SET_ORDER);
			foreach($matches as $mkey => $match) {
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
			$regExp = '#param_power_area.*?'
					. 'class="hp">(?<hp>[\d]+)<.*?'
					. '(?:class="mp">(?<mp>[\d]+)<.*?)?'
					. '(?:class="cp">(?<cp>[\d]+)<.*?)?'
					. '(?:class="gp">(?<gp>[\d]+)<.*?)?'
					. 'class="tp">(?<tp>[\d]+)<.*?'
					. '#';
			if(preg_match($regExp, $html, $matches)){
				$this->clearRegExpArray($matches);
				$character->attributes['hp'] = intval($matches['hp']);
				$character->attributes['mp'] = intval($matches['mp']);
				$character->attributes['cp'] = intval($matches['cp']);
				$character->attributes['gp'] = intval($matches['gp']);
				$character->attributes['tp'] = intval($matches['tp']);
			}

            # Minions and Mounts
			$mountHtml = $this->trim($html, '<!-- Mount -->', '<!-- //Mount -->');
			$regExp = "#<a.*?title=\"(?<name>.*?)\".*?<img.*?src=\"(?<icon>.*?)\?.*?>#";

			preg_match_all($regExp, $mountHtml, $matches, PREG_SET_ORDER);
			foreach($matches as $mkey => $match) {
				$this->clearRegExpArray($match);
				$character->mounts[] = $match;
			}

			$minionHtml = $this->trim($html, '<!-- Minion -->', '<!-- //Minion -->');
			$regExp = "#<a.*?title=\"(?<name>.*?)\".*?<img.*?src=\"(?<icon>.*?)\?.*?>#";

			preg_match_all($regExp, $minionHtml, $matches, PREG_SET_ORDER);
			foreach($matches as $mkey => $match) {
				$this->clearRegExpArray($match);
				$character->minions[] = $match;
			}

            // dust up
            $character->clean();

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
                        $achievement->kinds[$type] = $achievement->kinds[$type] + $points;
                        $achievement->countCurrent = $achievement->countCurrent + 1;
                    }

                    // Increment overall total
                    $achievement->pointsTotal = $achievement->pointsTotal + $points;
                    $achievement->countTotal = $achievement->countTotal + 1;
                }
            }
        }
        else
        {
            // New character object
            $achievement = new Achievements();

            // Setup url and get html
            $url = $this->urlGen('achievements', [ '{id}' => $id ]);
            $html = $this->trim($this->curl($url), '<!-- tab menu -->', '<!-- //base -->');

            $p = new Parser($html);

            // Begin parsing/populating character
            $achievement->pointsCurrent = $p->find('txt_yellow')->numbers();
            $achievement->legacy = (strlen($p->find('legacy')->html()) > 0) ? true : false;

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
			$achievement->legacy = (preg_match('#class="legacy"#',$html) === 1);
			$isLegacy = $achievement->legacy;
			# Achievments
			$possibleClasses = array();
			$regExp = "#<li><div class=\"(?<achieved>.*?)\">.*?src=\"(?<icon>.+?)\?.*?achievement_name.*?>(?<name>.*?)</span>(?<dateHTML>.*?)achievement_point.*?>(?<points>[\d]+)</div>.*?<a.*?href=\"/lodestone/character/[\d]+/achievement/detail/(?<id>[\d]+)/\".*?</li>#";

			$achievmentMatches = array();
			preg_match_all($regExp, $html, $achievmentMatches, PREG_SET_ORDER);
			foreach($achievmentMatches as $mkey => $match) {
				$obtained = $match['achieved'] == "already" ? true : false;
				if($match['achieved'] == "already"){
					preg_match('#ldst_strftime\(([\d\.]+),#',$match['dateHTML'],$dateMatch);
					$time = filter_var($dateMatch[1], FILTER_SANITIZE_NUMBER_INT);
				}else{
					$time = 0;
				}
				$points = filter_var($match['points'], FILTER_SANITIZE_NUMBER_INT);
				$achievement->list[] =
				[
					'id' => $match['id'],
					'icon' => $match['icon'],
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
     * Get onlinestatus of server(s)
     * @param string $datacenter
     * @param string $server
     * @return array
     */
    public function Worldstatus($datacenter = null, $server = null) {
        $worldStatus = array();
        $url = $this->urlGen('worldstatus', []);
        // Get Data from URL
        \phpQuery::newDocumentFileHTML($url);

        if(!is_null($datacenter)) {
            $datacenterNode = pq(sprintf("#server_status div.area_body:contains('%s')", $datacenter));
            if(!is_null($server)) {
                $worldStatus[$datacenter][$server] = trim($datacenterNode->next('div.area_inner_header')->find(sprintf('tr.worldstatus_1:contains("%s") td:eq(1)', $server))->text());
            }else {
                $pqTable = $datacenterNode->next('div.area_inner_header')->find('table');
                foreach($pqTable->find('tr.worldstatus_1') as $tableRow) {
                    $pqTableRow = pq($tableRow);
                    $server = trim($pqTableRow->find('td:first>div')->text());
                    $status = trim($pqTableRow->find('td:last>span')->text());
                    $worldStatus[$datacenter][$server] = trim($status);
                }
            }
        }else {
            $dataArray = [];
            // Loop it
            foreach(pq('#server_status div.area_body') as $node) {
                $pqNode = pq($node);
                $datacenter = trim(str_replace("Data Center: ", "", $pqNode->find('div.text-headline:first')->text()));
                $pqTable = $pqNode->next('div.area_inner_header')->find('table');
                foreach($pqTable->find('tr.worldstatus_1') as $tableRow) {
                    $pqTableRow = pq($tableRow);
                    $server = trim($pqTableRow->find('td:first>div')->text());
                    $status = trim($pqTableRow->find('td:last>span')->text());
                    $dataArray[$datacenter][$server] = trim($status);
                }
            }
            $worldStatus = $dataArray;
        }
        return $worldStatus;
    }
}