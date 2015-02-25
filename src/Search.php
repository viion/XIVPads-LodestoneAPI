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
        if (is_numeric($nameOrId))
        {
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

            foreach($p->findAll('ic_class_wh24_box', 3, 'base_inner') as $i => $node)
            {
                $node = new Parser($node);
                $name = $node->find('ic_class_wh24_box')->text();

                if ($name)
                {
                    $exp   = explode(' / ', $node->find('ic_class_wh24_box', 2)->text());

                    $character->classjobs[] =
                    [
                        'icon' => $node->find('ic_class_wh24_box')->attr("src"),
                        'name' => $name,
                        'level' =>  $node->find('ic_class_wh24_box', 1)->numbers(),
                        'exp_current' => intval($exp[0]),
                        'exp_total' => intval($exp[1]),
                    ];
                }

                unset($node);
            }

            /*
            foreach(pq('.class_list tr') as $i => $node)
            {
                $node = pq($node);

                $character->classjobs[] =
                [
                    'icon' => $node->find('td')->eq(0)->find('img')->attr('src'),
                    'name' => $node->find('td')->eq(0)->text(),
                    'level' => $node->find('td')->eq(1)->text(),
                    'exp_current' => filter_var(explode('/', $node->find('td')->eq(2)->text())[0], FILTER_SANITIZE_NUMBER_INT),
                    'exp_total' => filter_var(explode('/', $node->find('td')->eq(2)->text())[1], FILTER_SANITIZE_NUMBER_INT),
                ];

                // check next to table
                if (strlen($node->find('td')->eq(3)->text()) > 0)
                {
                    $character->classjobs[] =
                    [
                        'icon' => $node->find('td')->eq(3)->find('img')->attr('src'),
                        'name' => $node->find('td')->eq(3)->text(),
                        'level' => $node->find('td')->eq(4)->text(),
                        'exp_current' => filter_var(explode('/', $node->find('td')->eq(5)->text())[0], FILTER_SANITIZE_NUMBER_INT),
                        'exp_total' => filter_var(explode('/', $node->find('td')->eq(5)->text())[1], FILTER_SANITIZE_NUMBER_INT),
                    ];
                }
            }

            foreach(pq('.contents:not(.none) .item_detail_box') as $i => $node)
            {
                $node = pq($node);

                $character->gear[$i] =
                [
                    'name' => $node->find('.item_name_right h2')->text(),
                    'icon' => $node->find('.ic_reflection_box_64 img')->eq(1)->attr('src'),
                    'slot' => $node->find('.category_name')->text(),
                    'lodestone' => explode('/', $node->find('.bt_db_item_detail.m0auto.mb8 a')->attr('href'))[5],
                    'item_level' => filter_var($node->find('.pt3.pb3')->text(), FILTER_SANITIZE_NUMBER_INT),
                ];
            }

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

            // Cleanup to reduce code repeating
            // added strip_tags because of htmltags in arrayKeys
            // 25.02.2015 @JohnRamboTSQ
            foreach(pq('#power_gauge li,.param_list_attributes li, .param_list_elemental li') as $i => $node)
            {
                $node = pq($node);
                $attr = strip_tags(str_ireplace(' clearfix', null, $node->attr('class')));
                $character->attributes[$attr] = filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
            }
            foreach(pq('.param_list li') as $i => $node)
            {
                $node = pq($node);
                $attr = strip_tags(strtolower(str_ireplace(' ', '-', $node->find('span')->eq(0))));
                $character->attributes[$attr] = filter_var($node->find('span')->eq(1), FILTER_SANITIZE_NUMBER_INT);
            }
            foreach(pq('.minion_box')->eq(0)->find('a') as $i => $node)
            {
                $node = pq($node);
                $character->minions[$i] =
                [
                    'name' => $node->attr('title'),
                    'icon' => $node->find('img')->attr('src'),
                ];
            }
            foreach(pq('.minion_box')->eq(1)->find('a') as $i => $node)
            {
                $node = pq($node);
                $character->mounts[$i] =
                [
                    'name' => $node->attr('title'),
                    'icon' => $node->find('img')->attr('src'),
                ];
            }
            */

            // show
            $p->show();

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