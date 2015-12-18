var functions = require('../functions');

var apiItems =
{
    getUrl: function(type, string) {

        var urls =
        {
            search: '/lodestone/playguide/db/item/?db_search_category=item&category2=&q={string}'.replace('{string}', string),
            item: '/lodestone/playguide/db/item/{string}/'.replace('{string}', string),
        }

        return urls[type];
    },

    getSearch: function($) {
        var results = [];
        $('.table_black #character tbody tr').each(function() {
            $dom = $(this);

            var link = $dom.find('.db_popup.highlight').attr('href');
                item_id = link.split('/')[5];

            results.push({
                id: item_id,
                link: link,
                name: $dom.find('td:nth-child(1) .db_popup.highlight').text(),
                img: $dom.find('td:nth-child(1) .ic_reflection_box img').attr('src'),
                kind: $dom.find('td:nth-child(1) .ic_link_txt span a:nth-child(1)').text(),
                category: $dom.find('td:nth-child(1) .ic_link_txt span a:nth-child(2)').text(),
                item_level: parseInt($dom.find('td:nth-child(2)').text()),
                required_level: parseInt($dom.find('td:nth-child(3)').text()),
            });
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim()),
                end: parseInt($('.current_list .show_end').eq(0).text().trim()),
                total: parseInt($('.current_list .total').eq(0).text().trim()),
            },
            version: $('.area_footer .right').text().split(':')[1].trim(),
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },

    getData: function($) {
        // detail box
        var $box = $('.item_detail_box');

        // flags
        var flags = '';
        $box.find('ul.eorzeadb_tooltip_ml12 li').each(function() { flags = flags + ' ' + $(this).text().trim(); });
        flags = flags.replace(/\u00a0/g, " ").split(' ')
        flags = functions.clean(flags, '');

        // category
        var category = $box.find('.item_name_area').text().trim().split("\n"),
            category = functions.clean(category, ''),
            categoryOffset = 3;

        if (typeof category[categoryOffset] === 'undefined') {
            categoryOffset = 1;
        }

        // sells for
        var sellsfor = $box.find('.popup_w412_body_inner').last().text().trim().split(' '),
            sellsfor = functions.clean(sellsfor, '');

        var color = $box.find('.item_name_area h2').attr('class').trim().split(' '),
            color = functions.clean(color, '')[1].trim().split('_')[0];

        // data
        var data =
        {
            name: $box.find('.item_name_area h2').text(),
            color: color,
            category: category[categoryOffset].trim(),
            icon: $box.find('.item_ic_box img.sys_nq_element').attr('src').trim(),
            is_unique: $box.find('.item_name_area .rare').length > 0 ? 1 : 0,
            is_untradable: $box.find('.item_name_area .ex_bind').length > 0 ? 1 : 0,
            is_convertible: flags[1] == 'Yes' ? 1 : 0,
            is_projectable: flags[3] == 'Yes' ? 1 : 0,
            is_desynthesizable: flags[5] == 'Yes' ? 1 : 0,
            is_dyeable: flags[7] == 'Yes' ? 1 : 0,
            is_crestable: flags[9] == 'Yes' ? 1 : 0,
            sell_nq: sellsfor[2] ? parseInt(sellsfor[2]) : false,
            sell_hq: sellsfor[6] ? parseInt(sellsfor[6]) : false,
        }

        // related duties
        var $footers = $('.w480_footer.mt10');
        $footers.each(function() {
            $footer = $(this);

            // related duties
            if ($footer.find('h3').text() == 'Related Duties')
            {
                data.duties = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $name = $row1.find('a.db_popup'),
                        $kind = $row1.find('.small a:nth-child(1)'),
                        $category = $row1.find('.small a:nth-child(2)');

                    var id = $name.attr('href').split('/'),
                        id = functions.clean(id, '')[4];

                    data.duties.push({
                        id: id,
                        name: $name.text().trim(),
                        url: $name.attr('href'),
                        kind_name: $kind.text().trim(),
                        kind_url: $kind.attr('href'),
                        category_name: $category.text().trim(),
                        category_url: $category.attr('href').trim(),
                    });
                });
            }

            // crafting logs
            if ($footer.find('h3').text() == 'Crafting Log')
            {
                data.craftinglog = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $row2 = $(this).find('td:nth-child(2)'),
                        $row3 = $(this).find('td:nth-child(3)'),
                        $name = $row1.find('a');

                    var id = $name.attr('href').split('/'),
                        id = functions.clean(id, '')[4];

                    console.log($row2.text().trim());

                    data.craftinglog.push({
                        id: id,
                        icon: $row1.find('img').attr('src'),
                        name: $name.text().trim(),
                        link: $name.attr('href'),
                        recipe_level: ($row2.text().trim() != '-') ? parseInt($row2.text().trim()) : false,
                        recipe_stars: parseInt($row2.find('.ic_recipe_star_02').length),
                        item_level: ($row3.text().trim() != '-') ? parseInt($row3.text().trim()) : false,
                    });
                });
            }

            // selling npcs
            if ($footer.find('h3').text() == 'Selling NPC')
            {
                data.selling = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $row2 = $(this).find('td:nth-child(2)');

                    var npc_id = $row1.find('a').attr('href').split('/'),
                        npc_id = functions.clean(npc_id, '')[5];

                    var area_id = $row2.find('a').attr('href').split('='),
                        area_id = functions.clean(area_id, '')[2];

                    data.selling.push({
                        npc_name: $row1.find('a').text(),
                        npc_link: $row1.find('a').attr('href'),
                        npc_id: npc_id,
                        area_name: $row2.find('a').text(),
                        area_link: $row2.find('a').attr('href'),
                        area_id: area_id,
                        // area id matches in game ID
                    });
                });
            }

            // quests
            if ($footer.find('h3').text() == 'Related Quests')
            {
                data.quests = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $row2 = $(this).find('td:nth-child(2)'),
                        $row3 = $(this).find('td:nth-child(3)');

                    var kind_id = $row1.find('a:nth-child(1)').attr('href').split('='),
                        kind_id = functions.clean(kind_id, '')[1];

                    var category_id = $row1.find('a:nth-child(2)').attr('href').split('='),
                        category_id = functions.clean(category_id, '')[2];

                    data.quests.push({
                        kind_name: $row1.find('a:nth-child(1)').text(),
                        kind_url: $row1.find('a:nth-child(1)').attr('href'),
                        kind_id: kind_id,

                        category_name: $row1.find('a:nth-child(2)').text(),
                        category_url: $row1.find('a:nth-child(2)').attr('href'),
                        category_id: category_id,

                        area_name: $row2.text(),
                        level: parseInt($row3.text()),
                    });
                });
            }
        });

        return data;
    },
}

// Export it
module.exports = apiItems;