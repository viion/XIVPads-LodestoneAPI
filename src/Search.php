<?php
namespace Viion\Lodestone;

class Search
{
    use Urls;
    use Data;
    use Funky;

    /**
     * - Character
     *
     */
    public function Character($nameOrId, $world = null)
    {
        // if numeric, we dont search lodestone
        if (is_numeric($nameOrId)) {
            // New character object
            $character = new Character();

            // --- START -------------

            // Generate url and get html
            $url = $this->urlGen('characterProfile', [ '{id}' => $nameOrId ]);
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
            $character->freeCompanyId = filter_var($p->find('ic_crest_32', 6)->attr('href'), FILTER_SANITIZE_NUMBER_INT);
            $character->freeCompanyIcon = [
                $p->find('ic_crest_32', 2)->attr('src'),
                $p->find('ic_crest_32', 3)->attr('src'),
                $p->find('ic_crest_32', 4)->attr('src')
            ];

            # Class/Jobs

            $character->activeLevel = $p->find('"level"')->numbers();

            foreach($p->findAll('ic_class_wh24_box', 3, 'base_inner') as $i => $node) {
                // new node
                $node = new Parser($node);
                $name = $node->find('ic_class_wh24_box')->text();

                if ($name) {
                    $exp    = explode(' / ', $node->find('ic_class_wh24_box', 2)->text());
                    $icon   = explode('?', $node->find('ic_class_wh24_box')->attr("src"))[0];

                    $character->classjobs[] = [
                        'icon' => $icon,
                        'name' => $name,
                        'level' =>  $node->find('ic_class_wh24_box', 1)->numbers(),
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

            foreach($p->FindAll('param_list_attributes', 6) as $i => $node) {
                // new node
                $node = new Parser($node);
                $attr = ['str', 'dex', 'vit', 'int', 'mnd'];

                foreach($attr as $a) {
                    $character->attributes[$a] = $node->find($a)->numbers();
                }
            }

            foreach($p->FindAll('param_list_elemental', 8) as $i => $node) {
                // new node
                $node = new Parser($node);
                $attr = ['fire', 'ice', 'wind', 'earth', 'thunder', 'water'];

                foreach($attr as $a) {
                    $character->attributes[$a] = $node->find($a)->numbers();
                }
            }

            foreach($p->findAll('param_list', 4, 'param_list_elemental') as $i => $node) {
                // new node
                $node = new Parser($node);

                foreach($node->findAll('clearfix',1) as $j => $n)
                {
                    $n = new Parser($n);
                    $name = strtolower(str_replace(range(0,9), null, $n->find('clearfix')->text()));
                    $name = str_replace(' ', '-', trim($name));
                    $value = $n->find('clearfix')->numbers();
                    $character->attributes[$name] = intval(trim($value));
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
                        'icon' => $n->find('ic_reflection_box', 1)->attr('src'),
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

            // return
            return $character;
        }
        else
        {
            $searchName = str_ireplace(' ', '+',  $nameOrId);

            // Generate url
            $url = $this->urlGen('characterSearch', [ '{name}' => $searchName, '{world}' => $world ]);

            // get doc
            \phpQuery::newDocumentFileHTML($url);

            // go through results
            foreach(pq('.table_black_border_bottom tr') as $i => $node)
            {
                $node = pq($node);
                $name = $node->find('h4.player_name_gold a')->text();
                $id = filter_var($node->find('h4.player_name_gold a')->attr('href'), FILTER_SANITIZE_NUMBER_INT);

                // match what was sent (lower both as could be user input)
                if (strtolower($name) == strtolower($nameOrId) && is_numeric($id))
                {
                    // recurrsive callback
                    return $this->Character($id);
                }
            }

            return false;
        }
    }

    /**
     * - Achievements
     */
    public function Achievements($id, $all = false)
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
                $url = $this->urlGen('achievementsKind', [ '{id}' => $id, '{kind}' => $kind ]);

                // get doc
                \phpQuery::newDocumentFileHTML($url);

                // Begin parsing/populating character
                $achievement->pointsCurrent = filter_var(pq('.total_point')->text(), FILTER_SANITIZE_NUMBER_INT);
                $achievement->legacy = (strlen(pq('.legacy')->html()) > 0) ? true : false;
                $isLegacy = $achievement->legacy;

                foreach(pq('.achievement_cnts ul.mt10 li') as $i => $node)
                {
                    $node = pq($node);

                    $points = filter_var($node->find('.achievement_point'), FILTER_SANITIZE_NUMBER_INT);
                    $obtained = (strlen($node->find('.achievement_date script')->text()) > 1) ? true : false;

                    $achievement->list[] =
                    [
                        'id' => explode('/', $node->find('a.button.bt_more')->attr('href'))[6],
                        'icon' => $node->find('.ic_achievement img')->attr('src'),
                        'name' => $node->find('.achievement_name')->text(),
                        'time' => $this->extractTime($node->find('.achievement_date script')->text()),
                        'obtained' => $obtained,
                        'points' => $points,
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
        }
        else
        {
            // Generate url
            $url = $this->urlGen('achievements', [ '{id}' => $id ]);

            // get doc
            \phpQuery::newDocumentFileHTML($url);

            // New character object
            $achievement = new Achievements();

            // Begin parsing/populating character
            $achievement->pointsCurrent = filter_var(pq('.total_point')->text(), FILTER_SANITIZE_NUMBER_INT);
            $achievement->legacy = (strlen(pq('.legacy')->html()) > 0) ? true : false;

            // Recent
            foreach(pq('.achievement_list li') as $i => $node)
            {
                $node = pq($node);

                $achievement->recent[] =
                [
                    'id'   => explode('/', $node->find('.ic_achievement a')->attr('href'))[6],
                    'icon' => $node->find('.ic_achievement a img')->attr('src'),
                    'name' => $node->find('.achievement_txt a')->text(),
                    'time' => $this->extractTime($node->find('.achievement_date script')->text()),
                ];
            }
        }

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