<?php
namespace Viion\Lodestone;

trait Parse
{
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
            $character->avatarTimestamp = $nameMatches['avatarTimestamp'];
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
                . "(?(?=<dl.*?/gcrank/.*?</dl>).*?<dt class=\"icon\">" . $this->getRegExp('image','grandCompanyIcon') . "</dt>.*?"
                . "txt_name\">(?<grandCompany>.*?)/(?<grandCompanyRank>.*?)</dd></dl>).*?"
                . "(?(?=<dl).*?<div class=\"ic_crest_32\"><span>" . $this->getRegExp('image','freeCompanyIcon1') . $this->getRegExp('image','freeCompanyIcon2') . '(?:' . $this->getRegExp('image','freeCompanyIcon3') . ')?' . "</span></div>.*?"
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
            $character->portraitTimestamp = $profileMatches["portraitTimestamp"];
        }

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
                . '(?(?=<ul class="basic_bonus")<ul class="basic_bonus">(?<bonuses>.*?)</ul>.*?)'
                . '(?(?=<ul class="list_1col)<ul class="list_1col.*?>'
				. '<li class="clearfix".*?><div>(?<durability>.*?)%</div></li>'
                . '<li class="clearfix".*?><div>(?<spiritbond>.*?)%</div></li>'
                . '<li class="clearfix".*?><div>(?<repairClass>[\w]+?)\s[\w\.]+?\s(?<repairLevel>\d*?)</div></li>'
                . '<li class="clearfix".*?><div>(?<materials>.*?)<\/div><\/li>.*?)'
                /** @TODO mutlilanguage **/
                . '(?(?=<ul class="ml12")<ul class="ml12"><li>[\s\w]+?:\s(?<convertible>Yes|No)[\s\w]+?:\s(?<projectable>Yes|No)[\s\w]+?:\s(?<desynthesizable>Yes|No)[\s\w]*?<\/li><\/ul>.*?)'
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
			if(array_key_exists('bonuses', $match)){
				foreach($match['bonuses'] as $b){
					$keyCleaned = strtolower(str_ireplace(' ', '-', $b['type']));
					if(!array_key_exists($keyCleaned, $character->gearBonus)){
						$character->gearBonus[$keyCleaned] = [
							'total' => 0,
							'items' => []
						];
					}
					$character->gearBonus[$keyCleaned]['total'] += intval($b['value']);
					$character->gearBonus[$keyCleaned]['items'][] = [
						'value' => intval($b['value']),
						'name' => $match['name']
					];
				}
			}

            $character->gear[] = $match;

            if ($match['slot'] != 'Soul Crystal') {
                $iLevelTotal = $iLevelTotal + $match['ilv'];
                $iLevelArray[] = $match['ilv'];
                $iLevelCalculated[] = $match['ilv'];

                if (in_array($match['slot'], $this->getTwoHandedItems())) {
                    $iLevelCalculated[] = $match['ilv'];
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

        if (isset($itemsMatch[0])) {
            if (preg_match('#('. implode('?|',$possibleClasses).')#i',$itemsMatch[0]['slot'],$activeClassMatch) === 1) {
                $character->activeClass = $activeClassMatch[1];
            }
        }

        $character->gearStats = [
            'total' => $iLevelTotal,
            'average' => isset($iLevelCalculated) ? floor(array_sum($iLevelCalculated) / 13) : 0,
            'array' => $iLevelArray,
        ];
        //Unsets
        unset($gearHtml);
        unset($gearRegExp);
        unset($itemsMatch);
        unset($activeClassMatch);
        unset($possibleClasses);
        unset($iLevelArray);
        unset($iLevelCalculated);

        return $character;

    }
    private function _parseServerstatus($datacenterTableHTML,$server=null){
        $serverMatches = array();
        $serverRegExp = is_null($server) ? '\w+?' : $server;
        /**
         * @todo find out which statusnumber is for what
         */
        $statusNumber = array(
            1 => "online",
            3 => "maintenance"
        );
        $regExp = '#relative">(?<server>'.$serverRegExp.')</div>.*?ic_worldstatus_(?<statusNumber>\d+)">(?<status>[\w\s]+)</span>#';
        preg_match_all($regExp, $datacenterTableHTML, $serverMatches, PREG_SET_ORDER);
        $this->clearRegExpArray($serverMatches);
        return $serverMatches;
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
                    . '<div>(?:<span class="tag">\[(?<type>.*?)\]</span>)?'
                    . '<a href="/lodestone/news/detail/(?<linkHash>[\w\d]+)".*?>(?<body>.*?)</a></div>.*?'
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
                    'icon' => $member['rankIcon'],
                    ),
                'class' => array(
                    'icon' => $member['classIcon'],
                    'level' => $member['classLevel']
                )
            );
            if(array_key_exists('gcIcon', $member)){
                $members[$key]['grandcompany'] = array(
                    'icon' => $member['gcIcon'],
                    'name' => $member['gcName'],
                    'rank' => $member['gcRank']
                );
            }
        }
        return $members;
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
                    'icon' => $member['classIcon'],
                    'level' => $member['classLevel']
                )
            );
            if(array_key_exists('gcRankIcon', $member)){
                $members[$key]['grandcompany'] = array(
                    'icon' => $member['gcRankIcon'],
                    'name' => $member['gcName'],
                    'rank' => $member['gcRank']
                );
            }
            if(array_key_exists('fcName', $member)){
                $members[$key]['freecompany'] = array(
                    'icon' => array(
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

