<?php
namespace Viion\Lodestone;

class Search
{
    use Urls;
    use Data;

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

            // Parse
            \phpQuery::newDocumentFileHTML($url);

            // New character object
            $character = new Character();

            // Begin parsing/populating character
            $character->id = pq('.player_name_thumb a')->attr('href');
            $character->name = pq('.player_name_txt h2 a')->text();
            $character->server = pq('.player_name_txt h2 span')->text();
            $character->title = pq('.player_name_txt h2 .chara_title')->text();
            $character->avatar = pq('.player_name_thumb a img')->attr('src');
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

            show($character->dump());
        }
        else
        {
            $url = $this->urlGen('characterSearch', [ '{name}' => $nameOrId, '{world}' => $world ]);

            show($url);
        }

    }
}