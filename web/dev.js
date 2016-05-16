// - - - - - - - - - - - - - - - - - - - -
// This dev file is just so I can get a
// bunch of correct jquery stuff, copied
// into their respective parsing scripts
// - - - - - - - - - - - - - - - - - - - -
// Clean function can be used to delete
// stuff out of an array
var functions = {
    clean: function(string, deleteValue) {
        for (var i = 0; i < string.length; i++) {
            if (string[i].trim() == deleteValue) {
                string.splice(i, 1);
                i--;
            }
        }
        return string;
    },
}
function ldst_strftime(time, stamp) {}
// - - - - - - - - - - - - - - - - - - - -
$.get('/web/dev-html.html', function(data) {
    var $dom = $('.dev'); $dom.html(data);
    // - - - - - - - - - - - - - - - - - - - -
    // Your dev code goes here, and is then
    // copied inside an api/api-<entity> file
    // once you've got it parsing eveything
    //
    // Use the $dom variable as the parent
    // for scraping the data.
    //
    // Use normal jquery syntax
    //
    // - - - - - - - - - - - - - - - - - - - -

    // vars to use
    var $head = $dom.find('.eorzea_head'),
        $info = $dom.find('.eorzea_info'),
        $battles = $dom.find('.table_type_inner.silver, .table_type_inner.gold'),
        $chests = $dom.find('.table_type_inner:not(.silver):not(.gold) .cell_count'),
        $info = $dom.find('.eorzea_info');

    var data =
    {
        banner: $dom.find('.eorzea_visual > img').attr('src').trim(),
        type: $head.find('.lname_type').text().trim(),
        name: $head.find('.lname_name').text().trim(),
        level: $head.find('.eorzea_level > span').text().trim(),
        description: $info.find('> p.pd_5').text().trim(),
        info: [],
        totals: {
            tokens: 0,
            items: 0,
            bosses: 0,
            enemies: 0,
            rewards: 0,
        },
        battles: [],
        chests: [],
        rewards: [],
    }

    //
    // Loop through battles
    //
    $battles.each(function() {
        var $node = $(this),
            $enemies = $node.find('.list_db_boss li'),
            $tokens = $node.find('.cell_count .allagan_box'),
            $items = $node.find('.cell_count .sys_treasure_box_popup .base_body .treasure_items_wrap > div'),
            $rewards = $node.find('.boss_reward_item .boss_reward_item_wrapper');

        //
        // Result
        //
        var result = {
            // type: $node.find('.inner_brd > h3.type_ttl').text(),
            enemies: [],
            tokens: [],
            items: [],
            rewards: [],
        };

        // loop through all tokens
        $tokens.each(function() {
            var $node = $(this),
                value = parseInt($node.find('.value_box > div.value').text().trim().replace(/,/g, ''));

            result.tokens.push({
                icon: $node.find('> img').attr('src').trim(),
                name: $node.find('.value_box > div:first-of-type').text().trim(),
                value: value,
            });

            // increment tokens
            data.totals.tokens += value;
        });

        // loop through enemies
        $enemies.each(function() {
            var $node = $(this),
                bossUrl = $node.find('> a').attr('href').split('/'),
                bossType = functions.clean(bossUrl, '')[4],
                bossId = functions.clean(bossUrl, '')[5],
                bossIcon = $node.find('> img').attr('src'),
                isBoss = $node.hasClass('boss');

            result.enemies.push({
                id: bossId,
                type: bossType,
                is_boss: isBoss,
                name: $node.find('> a').text().trim(),
                icon: bossIcon ? bossIcon.trim() : null,
            });

            // increment boss / enemy counts
            isBoss ? data.totals.bosses += 1 : data.totals.enemies += 1;
        });

        // Loop through tiems
        $items.each(function() {
            $(this).find('.item_box').each(function() {
                var $node = $(this),
                    itemUrl =  $node.find('> div:first-of-type a').attr('href').split('/'),
                    itemType = functions.clean(itemUrl, '')[3],
                    itemId = functions.clean(itemUrl, '')[4];

                // add item
                result.items.push({
                    id: itemId,
                    type: itemType,
                    icon: $node.find('> div:first-of-type img').attr('src').trim(),
                    name: $node.find('> div:last-of-type a').text().trim(),
                });

                // increment items
                data.totals.items += 1;
            });
        });

        // loop through rewards
        $rewards.each(function() {
            var $node = $(this),
                itemUrl =  $node.find('.ic_link_txt a').attr('href').split('/'),
                itemType = functions.clean(itemUrl, '')[3],
                itemId = functions.clean(itemUrl, '')[4];

            // add item
            result.rewards.push({
                id: itemId,
                type: itemType,
                icon: $node.find('.boss_reward_item_inner > div:first-of-type img').attr('src').trim(),
                name: $node.find('.ic_link_txt a').text().trim(),
                conditions: $node.find('.txt_reward_item_condition').text().trim(),
            });

            // increment items
            data.totals.rewards += 1;
        });

        // add to mini battle
        data.battles.push(result);
    });

    //
    // Loop through chests
    //
    $chests.each(function() {
        var $node = $(this).find('.base_body');

        var $cords = $('<div></div>').html($node.find('.base_inner .area_inner_body > div:first-of-type').html());
        $cords.find('> span').remove();

        var result = {
            name: $node.find('.area_body .area_footer span').text().trim(),
            icon: $node.find('.area_body .area_footer img').attr('src').trim(),
            position: $cords.text().trim(),
            items: [],
        }

        // loop through items
        $node.find('.treasure_items_wrap .item_box').each(function() {
            var $node = $(this),
            itemUrl =  $node.find('> div:first-of-type a').attr('href').split('/'),
            itemType = functions.clean(itemUrl, '')[3],
            itemId = functions.clean(itemUrl, '')[4];

            // add item
            result.items.push({
                id: itemId,
                type: itemType,
                icon: $node.find('> div:first-of-type img').attr('src').trim(),
                name: $node.find('> div:last-of-type a').text().trim(),
            });

            // increment items
            data.totals.items += 1;
        });

        // add to mini battle
        data.chests.push(result);
    });

    //
    // Loop through info
    //
    $info.find('> ul.pd_5 li').each(function() {
        data.info.push($(this).text().trim());
    });

    //
    // if guild hest
    //
    if (data.type == 'Guildhests') {
        var $completion = $dom.find('.eorzea_info .table_type_inner').eq(0),
            $bonus = $dom.find('.eorzea_info .table_type_inner').eq(1);

        var rewards = {
            completion: [],
            bonus: [],
        }

        rewards.completion.push({
            type: 'exp', value: parseInt($completion.find('.quest_exp .value').text().replace(/,/g, '')),
        });

        rewards.completion.push({
            type: 'gil', value: parseInt($completion.find('.quest_gil .value').text().replace(/,/g, '')),
        });

        rewards.bonus.push({
            type: 'exp', value: parseInt($bonus.find('.quest_exp .value').text().replace(/,/g, '')),
        });

        rewards.bonus.push({
            type: 'gil', value: parseInt($bonus.find('.quest_gil .value').text().replace(/,/g, '')),
        });

        data.rewards.push(rewards);
    }

    //
    // if PVP
    //
    if (data.type == 'PvP') {
        var $rows = $dom.find('.eorzea_info .table_type_inner'),
            $victor = $rows.eq(0), $loser = $rows.eq(1);

        // if victor exists, then the results are win/loose
        // otherwise the results are rank based
        if ($victor.find('h4').text() == 'Victor') {
            var rewards = {
                victor: [],
                loser: [],
            }

            rewards.victor.push({
                type: 'exp', value: parseInt($victor.find('.quest_exp:not(.pvp_point) .value').text().replace(/,/g, '')),
            });

            rewards.victor.push({
                type: 'gil', value: parseInt($victor.find('.quest_gil .value').text().replace(/,/g, '')),
            });

            rewards.victor.push({
                type: 'marks', value: parseInt($victor.find('.pvp_point .value').text().replace(/,/g, '')),
            });

            rewards.loser.push({
                type: 'exp', value: parseInt($loser.find('.quest_exp:not(.pvp_point)  .value').text().replace(/,/g, '')),
            });

            rewards.loser.push({
                type: 'gil', value: parseInt($loser.find('.quest_gil .value').text().replace(/,/g, '')),
            });

            rewards.loser.push({
                type: 'marks', value: parseInt($loser.find('.pvp_point .value').text().replace(/,/g, '')),
            });
        } else {
            var rewards = [];
            $rows.each(function() {
                var $node = $(this);

                rewards.push({
                    name: $node.find('> h4').text(),
                    exp: parseInt($node.find('.list_reward .pvp_exp .value').text().replace(/,/g, '')),
                    marks: parseInt($node.find('.list_reward .pvp_point .value').text().replace(/,/g, '')),
                });
            });
        }

        data.rewards = rewards;
    }

    console.log(data);

    // - - - - - - - - - - - - - - - - - - - -
    // End
    // - - - - - - - - - - - - - - - - - - - -
});
