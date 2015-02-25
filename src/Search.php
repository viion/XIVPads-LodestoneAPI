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
            // Generate url
            $url = $this->urlGen('characterProfile', [ '{id}' => $nameOrId ]);

            // get doc
            \phpQuery::newDocumentFileHTML($url);

            // New character object
            $character = new Character();

            // Begin parsing/populating character
            $character->id = pq('.player_name_thumb a')->attr('href');
            $character->name = pq('.player_name_txt h2 a')->text();
            $character->world = pq('.player_name_txt h2 span')->text();
            $character->title = pq('.player_name_txt h2 .chara_title')->text();
            $character->avatar = pq('.player_name_thumb a img')->attr('src');
            $character->avatarLarge = str_ireplace('50x50', '96x96', $character->avatar);
            $character->portrait = pq('.bg_chara_264 a img')->attr('src');
            $character->portraitLarge = str_ireplace('264x360', '640x873', $character->portrait);

            $character->bio = pq('.txt_selfintroduction')->html();
            $character->race = explode('/', pq('.chara_profile_title')->text())[0];
            $character->clan = explode('/', pq('.chara_profile_title')->text())[1];
            $character->gender = explode('/', pq('.chara_profile_title')->html())[2];

            $character->nameday = pq('.chara_profile_box_info')->eq(0)->find('.txt_name')->eq(0)->text();
            $character->guardian = pq('.chara_profile_box_info')->eq(0)->find('.txt_name')->eq(1)->text();
            $character->guardianIcon = pq('.chara_profile_box_info')->eq(0)->find('img')->eq(0)->attr('src');
            $character->city = pq('.chara_profile_box_info')->eq(1)->find('.txt_name')->eq(0)->text();
            $character->cityIcon = pq('.chara_profile_box_info')->eq(1)->find('img')->eq(0)->attr('src');
            $character->grandCompany = explode('/', pq('.chara_profile_box_info')->eq(2)->find('.txt_name')->eq(0)->text())[0];
            $character->grandCompanyRank = explode('/', pq('.chara_profile_box_info')->eq(2)->find('.txt_name')->eq(0)->text())[1];
            $character->grandCompanyIcon = pq('.chara_profile_box_info')->eq(2)->find('img')->eq(0)->attr('src');
            $character->freeCompany = pq('.chara_profile_box_info')->eq(3)->find('.txt_name')->eq(0)->text();
            $character->freeCompanyIcon = [
                pq('.chara_profile_box_info')->eq(3)->find('img')->eq(0)->attr('src'),
                pq('.chara_profile_box_info')->eq(3)->find('img')->eq(1)->attr('src'),
                pq('.chara_profile_box_info')->eq(3)->find('img')->eq(2)->attr('src')
            ];

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

            foreach(pq('#power_gauge li') as $i => $node)
            {
                $node = pq($node);
                $attr = $node->attr('class');
                $character->attributes[$attr] = filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
            }

            foreach(pq('.param_list_attributes li') as $i => $node)
            {
                $node = pq($node);
                $attr = $node->attr('class');
                $character->attributes[$attr] = filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
            }

            foreach(pq('.param_list_elemental li') as $i => $node)
            {
                $node = pq($node);
                $attr = str_ireplace(' clearfix', null, $node->attr('class'));
                $character->attributes[$attr] = filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
            }

            foreach(pq('.param_list li') as $i => $node)
            {
                $node = pq($node);
                $attr = strtolower(str_ireplace(' ', '-', $node->find('span')->eq(0)));
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

            // dust up
            $character->clean();
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
}