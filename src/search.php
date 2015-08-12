<?php
namespace Viion\Lodestone;

class Search
{
    use Urls;
    use Data;
    use Funky;
    use Parse;

    // Set true to use basic parsing, otherwise uses advanced regex
    private $basicParsing = false;

    // if maint is on or not
    private $isMaintenance = false;

    /**
     * Switch to basic searching method
     */
    public function useBasicParsing()
    {
        $this->basicParsing = true;
    }

    /**
     * Is maintenance or not
     */
    public function isMaintenance() {
        return $this->isMaintenance;
    }

    /**
     * Search for a character, either by name/world or ID.
     *
     * @param $nameOrId - the name or id of the character
     * @param $world - the world/server of the character, string
     * @param $recurrsive = if true, the else wont fire, prevents recurrsion loops
     * @return Character - new character object.
     */
    public function Character($nameOrId, $world = null, $results = false, $recurrsive = false)
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
        else if (!$recurrsive)
        {
            $nameOrId = ucwords(strtolower($nameOrId));
            $world = ucwords(strtolower($world));
            $searchName = str_ireplace(' ', '+',  $nameOrId);

            // Generate url
            $url = $this->urlGen('characterSearch', [ '{name}' => $searchName, '{world}' => $world ]);
            $html = $this->trim($this->curl($url), '<!-- result -->', '<!-- /result -->');

            $p = new Parser($html);

            // go through found
            $found = [];
            foreach($p->findAll('thumb_cont_black_50', 'col3box_right') as $i => $node)
            {
                $node = new Parser($node);

                $id = filter_var($node->find('player_name_gold', 0)->attr('href'), FILTER_SANITIZE_NUMBER_INT);
                $data = explode(' (', $node->find('player_name_gold', 0)->text());
                $name = trim($data[0]);

                // Character
                $found[] =
                [
                    'id' => $id,
                    'name' => $name,
                    'world' => trim(str_replace(')', null, $data[1])),
                    'avatar' => explode('?', $node->find('thumb_cont_black_50', 1)->attr('src'))[0],
                ];

                // match what was sent (lower both as could be user input)
                if (!$results && strtolower($name) == strtolower($nameOrId) && is_numeric($id))
                {
                    // recurrsive callback
                    return $this->Character($id, null, false, true);
                }
            }

            // return found
            return $found;
        }

        return false;
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
            // Advanced searching
            return $this->advancedLinkshellParse($nameOrId);

        }
    }

    /**
     * Get achievements for a character
     *
     * @param $characterId - the id of the character
     * @param $all - all achievements or summary?
     * @return Achievement - an achievement object
     */
    public function Achievements($characterId, $all = false,$lastOnly=false)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($characterId)) {

            // If basic searching
            if ($this->basicParsing) {
                return $this->basicAchievementsParse($characterId, $all);
            }
			if($lastOnly===true){
				// Advanced searching
				return $this->advancedLastAchievementsParse($characterId);
			}
            // Advanced searching
            return $this->advancedAchievementsParse($characterId, $all);
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
        $html = $this->curl($url);
        $rawHtml = $this->trim($html, '<!-- contents -->', '<!-- //Minion -->');
		if($rawHtml == "<!-- contents -->" || $rawHtml == ""){
			return false;
		}

        // maint check
        if (stripos($html, 'h1.error_maintenance.na') !== false) {
            $this->isMaintenance = true;
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

                if (!$achievement->pointsCurrent) {
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
    private function advancedAchievementsParse($id, $all)
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
            $achievement->pointsCurrent = (array_key_exists('pointsCurrent',$achievementMatch) && $achievementMatch['pointsCurrent'] > 0 ) ? $achievementMatch['pointsCurrent'] : null;
            $achievement->legacy = (array_key_exists('legacy',$achievementMatch) && $achievementMatch['legacy'] == "legacy") ? true : false;
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

                if ($kind == 13) {
                    $achievement->legacyPointsTotal += $points;
                    if ($obtained) {
                        $achievement->legacyPoints += $points;
                    }
                }
			}
        }

        // Dust up
        $achievement->clean();

        // return
        return $achievement;
    }

    /**
     * Parse last achievement data, does it using regex, Faster
     *
     * @param $characterId - the character id
     * @param $all - all achievements or summary?
     * @return Achievement - an achievement object
     */
    private function advancedLastAchievementsParse($id)
    {
		$achievement = new \stdClass();
		// Generate url
		$url = $this->urlGen('achievements', [ '{id}' => $id]);
		$rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
		$html = html_entity_decode(preg_replace(array('#\s\s+#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);

		$achievementMatch = array();
		preg_match('#class="txt_yellow">(?<pointsCurrent>\d+)</strong>.*?(?<legacy>(?<=.)legacy|\#main)#',$html,$achievementMatch);
		$achievement->pointsCurrent = (array_key_exists('pointsCurrent',$achievementMatch) && $achievementMatch['pointsCurrent'] > 0 ) ? $achievementMatch['pointsCurrent'] : null;
		# Achievments
        $achievmentHtml = $this->trim($html, '<ul class="achievement_list">', '</ul>');
		$regExp = "#<li><div class=\"achievement_area_footer\">.*?<a.*?href=\"/lodestone/character/[\d]+/achievement/detail/(?<id>[\d]+)/\".*?" . $this->getRegExp('image','icon') . ".*?achievement_txt.*?>.*?<script>(?<dateHTML>.*?)</script>.*?</li>#";

		$achievmentMatches = array();
		preg_match_all($regExp, $achievmentHtml, $achievmentMatches, PREG_SET_ORDER);
		$this->clearRegExpArray($achievmentMatches);
		foreach($achievmentMatches as $mkey => $match) {
			$dateMatch = [];
			preg_match('#ldst_strftime\(([\d\.]+),#',$match['dateHTML'],$dateMatch);
			$time = filter_var($dateMatch[1], FILTER_SANITIZE_NUMBER_INT);
			unset($achievmentMatches[$mkey]['dateHTML']);
			$achievmentMatches[$mkey]['time'] = $time;
		}
		$achievement->last = $achievmentMatches;
        // return
        return $achievement;
    }

    /**
     * Get freecompany
     *
     * @param $freeCompanyId - the id of the freecompany
     * @param $members - with memberlist
     */
    private function advancedFreecompanyParse($freeCompanyId, $members = false)
    {
        // Generate url
        $url = $this->urlGen('freecompany', ['{id}' => $freeCompanyId]);
        $rawHtml = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
        $html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

        $freeCompany = new FreeCompany();
        $headerHtml = $this->trim($html, '<!-- playname -->', '<!-- //playname -->');

        $freeCompany->id = $freeCompanyId;
        $headerRegExp = '#' . $this->getRegExp('image','fcIcon1') . '.*?'
                        . $this->getRegExp('image','fcIcon2') . '.*?'
                        . '(?:' . $this->getRegExp('image','fcIcon3') . '.*?)?'
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

		// dust up
		$freeCompany->clean();

        return $freeCompany;
    }

    /**
     * Get linkshell
     *
     * @param $linkshellId - the id of the freecompany
     */
    private function advancedLinkshellParse($linkshellId)
    {
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

    /**
     * Get onlinestatus of servers
     * @return array
     */
    public function Worldstatus($datacenter = null, $server = null)
    {
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

    /**
     * get topics
     */
    public function Topics($hash = null)
    {
        if (is_null($hash)){
            return $this->_newsParser('topics');
        }

        return $this->_newsDetailParser('topics',$hash);
    }

    /**
     * get notices
     */
    public function Notices($hash = null)
    {
        if (is_null($hash)){
            return $this->_newsParser('notices');
        }

        return $this->_newsDetailParser('notices',$hash);
    }

    /**
     * get maintenance
     */
    public function Maintenance($hash = null)
    {
        if (is_null($hash)){
            return $this->_newsParser('maintenance');
        }

        return $this->_newsDetailParser('maintenance',$hash);
    }

    /**
     * get updates
     */
    public function Updates($hash = null)
    {
        if (is_null($hash)){
            return $this->_newsParser('updates');
        }

        return $this->_newsDetailParser('updates',$hash);
    }

    /**
     * get status
     */
    public function Status($hash = null)
    {
        if (is_null($hash)){
            return $this->_newsParser('status');
        }

        return $this->_newsDetailParser('status',$hash);
    }

	public function Devtracker()
    {
        $devtrackerMatch = array();
        $articles = array();
		// Generate url
		$url = 'http://forum.square-enix.com/ffxiv/forum.php';

		$rawHtml = $this->trim($this->curl($url), 'block_newposts_', '</ul>');
		$html = html_entity_decode(preg_replace(array('#\s\s+#s','#[\n\t]#s'),'', $rawHtml),ENT_QUOTES);

		$regExp = '#<li class=".*?widget_post_bit[^"]*">.*?'
				. '<a class="smallavatar comments_member_avatar_link".*?href="(?<authorLink>.*?-(?<author>[^\?]*?)(?:\?[^"]*)?)">' . $this->getRegExp('image','avatar') . '</a></div>.*?'
				. '<p class="widget_post_content">(?<teaser>.*?)</p>'
				. '<h5 class="widget_post_header"><a href="(?<teaserLink>.*?)" class="title">(?<teaserHeadline>.*?)</a></h5>'
				. '<div class="meta">(?<date>.*?)<span class="time">(?<time>[APM\:\d\s]+?)</span>.*?'
				. '</li>#';
		preg_match_all($regExp, $html, $devtrackerMatch, PREG_SET_ORDER);
        $breaks = array("<br />","<br>","<br/>", "\n");
		foreach($devtrackerMatch as $articleArray)
        {
			$this->clearRegExpArray($articleArray);
			$dateString = str_replace('-','/',$articleArray['date'])." " . $articleArray['time'];
			$timestamp = strtotime($dateString);
			$articles[$timestamp] = array(
				'date' => $timestamp,
				'avatar' => $articleArray['avatar'],
				'author' => $articleArray['author'],
				'authorLink' => $articleArray['authorLink'],
				'headline' => strip_tags(str_ireplace($breaks, " ", $articleArray['teaserHeadline'])),
				'teaser' => $articleArray['teaser'],
				'link' => $articleArray['teaserLink'],
			);
		}
		unset($devtrackerMatch);
		return $articles;
	}

	public function ItemDB($withDetails = false, $ids = null)
    {
		$items = array();
		$itemsData = array();
		if(is_null($ids)){
			// Generate url
			//$url = $this->urlGen('linkshell', ['{id}' => $linkshellId]);
			$url = 'http://eu.finalfantasyxiv.com/lodestone/playguide/db/item/?category2=3';
			$rawHtml = $this->trim($this->curl($url), '<table class="col_left_w300" id="character">', '</table>');
			$html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s', '#<!--\s*-->#s'), '', $rawHtml), ENT_QUOTES);

			$regExp = '#<tr>.*?'
					. '(?:(?<staining>staining)"></div>)?'
					. $this->getRegExp('image','icon') . '<div.*?'
					. '<span class="small">'
					. '(?:<a.*?>(?<category1>.*?)</a>)?.*?'
					. '(?:<a.*?>(?<category2>.*?)</a>)?.*?'
					. '</span>.*?'
					. '<a.*?href="/lodestone/playguide/db/item/(?<itemIds>[\d\w]+?)/".*?>(?<name>.*?)</a>.*?'
					. '<td class="col_center tc">(?<itemLvl>.*?)</td>'
					. '<td class="col_right tc">(?<gearLevel>.*?)</td>'
					. '</tr>#';
			$order = ($withDetails !== true) ? PREG_SET_ORDER : PREG_PATTERN_ORDER;
			preg_match_all($regExp, $html, $items, $order);
		}else{
			$items['itemIds'] = is_array($ids) ? $ids : array($ids);
		}


		if($withDetails !== true){
			$this->clearRegExpArray($items);
			$itemsData['items'] = $items;
		}else{
			$bonusRegExp = '#<li>(?<type>.*?)\s\+?(?<value>\-?\d+)</li>#i';
			$setBonusRegExp = '#<li class="set_bonus">\s*?(?<require>[^\s].*?):(?<type>.*?)\s\+?(?<value>\-?\d+)</li>#i';
			foreach($items['itemIds'] as $id){
				$jsUrl = sprintf('http://img.finalfantasyxiv.com/lds/pc/tooltip/1425544641/eu/item/%s.js',$id);
				$jsResponse = $this->curl($jsUrl);
				$Json = preg_replace('#eorzeadb\.pushup\((\{.*\})\)#','$1',$jsResponse);
				$data = json_decode($Json);
				$html = html_entity_decode(preg_replace(array('#\s\s+#s', '#[\n\t]#s', '#<!--\s*-->#s'), '', $data->html), ENT_QUOTES);

				$gearRegExp = '#.*?'
						. '<div class="name_area[^>].*?>.*?'
						. '(?:(?<staining>staining)"></div>)?'
						. '<img[^>]+?>' . $this->getRegExp('image','icon') . '<div.*?'
						. '<div class="item_element[^"]*?">'
						. '<span class="rare">(?<rare>[^<]*?)</span>'
						. '<span class="ex_bind">\s*(?<binding>[^<]*?)\s*</span></div>'
						. '<h2 class="item_name\s?(?<color>[^_]*?)_item">(?<name>[^<]+?)(?<hq><img.*?>)?</h2>(?<slot>[^<]*?)</div>.*?'
						. '<a href=".*?/item/(?<id>[\w\d]+?)/".*?>.*?</a></div>'
						. '(?(?=<div class="popup_w412_body_inner eorzeadb_tooltip_mb10">).*?'
						. '<div class="parameter\s?.*?"><strong>(?<parameter1>[^<]+?)</strong></div>'
						. '<div class="parameter\s?.*?"><strong>(?<parameter2>[^<]+?)</strong></div>'
						. '(?:<div class="parameter\s?.*?"><strong>(?<parameter3>[^<]+?)</strong></div>)?'
						. '</div>)'
						. '.*?<div class="eorzeadb_tooltip_pt3 eorzeadb_tooltip_pb3">.+?\s(?<ilv>[0-9]{1,3})</div>.*?'
						. '<div class="class_ok">(?<classes>[^<]*?)</div>'
						. '<div class="gear_level">[^\d]*?(?<gearlevel>[\d]+?)</div>.*?'
						. '<div class="list_1col eorzeadb_tooltip_mb10 clearfix">'
						. '(?(?=<ul class="basic_bonus")<ul class="basic_bonus">(?<bonuses>.*?)</ul></div>)'
						. '(?:<h3 class="eorzeadb_tooltip_ml12 eorzeadb_tooltip_txt_green">(?<set>.*?)</h3>'
						. '<ul class="list_1col eorzeadb_tooltip_mb10">(?<setBonuses>.*?)</ul>)?'
						. '</div>.*?'
						. '<li class="clearfix".*?><div>(?<repairClass>[\w]+?)\s[\w\.]+?\s(?<repairLevel>\d*?)</div></li>'
						. '<li class="clearfix".*?><div>(?<materials>.*?)<\/div><\/li>'
						. '(?:<li class="clearfix".*?><div>(?<meldingClass>[\w]+?)\s[\w\.]+?\s(?<meldingLevel>\d*?)</div></li>)?.*?'
						. '<ul class="eorzeadb_tooltip_ml12"><li>[\s\w]+?:\s(?<convertible>Yes|No)[\s\w]+?:\s(?<projectable>Yes|No)[\s\w]+?:\s(?<desynthesizable>Yes|No)[\s\w]*?</li></ul>'
						. '<ul class="eorzeadb_tooltip_ml12"><li>[\s\w]+?:\s(?<dyeable>Yes|No)[\s\w\-]+?:\s(?<crestWorthy>Yes|No)[\s\w]*?</li></ul>.*?'
						. '<div class="eorzeadb_tooltip_ml4"><span class="sys_nq_element">(?<sellable>.*?)</span>.*?</div>'
						. '.*?#u';
				$itemMatch = array();
				preg_match($gearRegExp, $html, $itemMatch);

				if(count($itemMatch) <=0)
					$itemsData['failed'][] = $id;

				$this->clearRegExpArray($itemMatch);
				// Basestats
				if($itemMatch['slot'] == 'Shield'){ // Shield
					$itemMatch['block_strength'] = $itemMatch['parameter1'];
					$itemMatch['block_rate'] = $itemMatch['parameter2'];
				}else if($itemMatch['parameter3'] == ""){ // Normalitem
					$itemMatch['defense'] = $itemMatch['parameter1'];
					$itemMatch['magical_defense'] = $itemMatch['parameter2'];
				}else{ // Weapon
					$itemMatch['damage'] = $itemMatch['parameter1'];
					$itemMatch['auto_attack'] = $itemMatch['parameter2'];
					$itemMatch['delay'] = $itemMatch['parameter3'];
				}
				unset($itemMatch['parameter1']);
				unset($itemMatch['parameter2']);
				unset($itemMatch['parameter3']);
				// HighQualityItem
				$itemMatch['hq'] = ($itemMatch['hq'] == "") ? false : true;

				//Bonuses
				if($itemMatch['bonuses'] != ""){
					$bonusMatch = array();
					preg_match_all($bonusRegExp,$itemMatch['bonuses'],$bonusMatch, PREG_SET_ORDER);
					$itemMatch['bonuses'] = $this->clearRegExpArray($bonusMatch);
				}else{
					$itemMatch['bonuses'] = null;
				}

				//Set Bonuses
				if($itemMatch['setBonuses'] != ""){
					$setBonusMatch = array();
					preg_match_all($setBonusRegExp,$itemMatch['setBonuses'],$setBonusMatch, PREG_SET_ORDER);
					$itemMatch['setBonuses'] = $this->clearRegExpArray($setBonusMatch);
				}else{
					$itemMatch['setBonuses'] = null;
				}


				$itemsData['items'][$id]['data'] = $itemMatch;
				$itemsData['items'][$id]['html'] = htmlentities($html);
				$itemsData['items'][$id]['regExp'] = htmlentities($gearRegExp);

			}
		}
		$itemsData['itemcount'] = count($itemsData['items']);
		return $itemsData;
	}

    public function ItemDBSearch($name)
    {
        $url = 'http://eu.finalfantasyxiv.com/lodestone/playguide/db/item/?db_search_category=item&category2=&q=%s';
        $url = sprintf($url, urlencode($name));

        $html = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');

        // get doc
        $p = new Parser($html);

        // get item id
        $foundItemId = null;
        foreach($p->findAll('db_popup highlight', 1) as $n)
        {
            $n = new Parser($n);

            $foundName = trim($n->find('db_popup')->text());
            if ($foundName == $name) {
                $foundItemId = trim($n->find('db_popup')->attr('href'));
            }
        }

        if (!$foundItemId) {
            return null;
        }

        $foundItemId = explode('/', $foundItemId);
        return isset($foundItemId[5]) ? $this->getItemFromLodestoneBasic($foundItemId[5]) : false;
    }

    private function getItemFromLodestoneBasic($id)
    {
        $url = 'http://eu.finalfantasyxiv.com/lodestone/playguide/db/item/%s/';
        $url = sprintf($url, trim($id));

        $html = $this->trim($this->curl($url), '<!-- #main -->', '<!-- //#main -->');
        $p = new Parser($html);

        //$p->show();

        $isUnique = strlen($p->find('class="rare"', 1)->text()) > 0 ? 1 : 0;
        $isEx = strlen($p->find('class="ex_bind"', 1)->text()) > 0 ? 1 : 0;
        $isReducible = strlen($p->find('Aetherial Reduction')->text()) > 0 ? 1 : 0;

        $redarr = false;
        if ($isReducible) {
            $reducibleText = $p->find('Aetherial Reduction')->text();
            $reducibleText = explode(' ', $reducibleText);
            $reducibleClass = trim(str_ireplace('Lv.', null, $reducibleText[3]));
            $reducibleLevel = intval(trim(filter_var($reducibleText[4], FILTER_SANITIZE_NUMBER_INT)));
            $reducibleClassId = array_search(strtolower($reducibleClass), $this->getClassListFull());

            $redarr = [
                'class' => $reducibleClass,
                'level' => $reducibleLevel,
                'classId' => $reducibleClassId,
            ];
        }

        $name = $p->find('h2 class="item_name', 2)->text();
        $name = trim(substr($name, 2));

        $nqIcon = $p->find('finalfantasyxiv.com/lds/pc/global/images/itemicon')->attr('src');
        $hqIcon = $p->find('finalfantasyxiv.com/lds/pc/global/images/itemicon', 1)->attr('src');
        $nqIcon = str_ireplace('http://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', null, $nqIcon);
        $hqIcon = str_ireplace('http://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', null, $hqIcon);
        $nqIcon = explode('.', $nqIcon)[0];
        $hqIcon = explode('.', $hqIcon)[0];

        $constraintsOne = $p->find('ul class="eorzeadb_tooltip_ml12"', 2)->text();
        $constraintsTwo = $p->find('ul class="eorzeadb_tooltip_ml12"', 5)->html();
        $constraintsTwo = str_ireplace('&amp;nbsp;', ' ', $constraintsTwo);
        $constraints = array_merge(explode(" ", $constraintsOne), explode(" ", $constraintsTwo));
        $constraints = array_values(array_filter($constraints));

        $sellsForNq = $p->find('span class="sys_nq_element"', 1)->numbers();
        $sellsForHq = $p->find('span class="sys_nq_element"', 3)->numbers();

        if ($hqIcon && !$nqIcon) {
            $nqIcon = $hqIcon;
            $hqIcon = null;
        }

        if (!$constraintsOne) {
            $constraintsOne = $p->find('eorzeadb_tooltip_ml4 eorzeadb_tooltip_mb10')->text();
            if ($constraintsOne) {
                $constraintsOne = explode(' ', $constraintsOne);
                $constraints[5] = trim($constraintsOne['1']);
            }
        }

        $arr = [
            'unique' => $isUnique,
            'untradable' => $isEx,
            'reducible' => $isReducible,
            'convertible' => isset($constraints[1]) && trim($constraints[1]) == 'Yes' ? 1 : 0,
            'projectable' => isset($constraints[3]) && trim($constraints[3]) == 'Yes' ? 1 : 0,
            'desynthesizable' => isset($constraints[5]) && trim($constraints[5]) == 'Yes' ? 1 : 0,
            'dyeable' => isset($constraints[7]) && trim($constraints[7]) == 'Yes' ? 1 : 0,
            'crestable' => isset($constraints[9]) && trim($constraints[9]) == 'Yes' ? 1 : 0,
        ];

        $data = [
            'id' => $id,
            'name' => $name,
            'icon' => $nqIcon,
            'icon_hq' => $hqIcon,
            'sell' => $sellsForNq,
            'sell_hq' => $sellsForHq,
            'constraints' => $arr,
            'reducible' => $redarr,
        ];

        //show($data);
        //$p->show();

        return $data;
    }
}