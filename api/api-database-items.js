var functions = require('../libs/functions');

var apiDatabaseItems =
{
    getUrl: function(type, string, page) {
        var urls =
        {
            search: '/lodestone/playguide/db/item/?db_search_category=item&category2=&q={string}&page={page}'.replace('{string}', string).replace('{page}', page),
            item: '/lodestone/playguide/db/item/{string}/'.replace('{string}', string),
        }

        return urls[type];
    },

    getSearch: function($) {
        var results = [];
        $('.db__l_main table tbody tr').each(function() {
            $dom = $(this);

            var link = $dom.find('.db-table__txt--detail_link').attr('href');
                item_id = link.split('/')[5];

            results.push({
                id: item_id,
                link: link,
                name: $dom.find('td:nth-child(1) .db-table__txt--detail_link').text(),
                img: $dom.find('td:nth-child(1) .db-list__item__icon .db-list__item__icon__item_image').attr('src'),
                kind: $dom.find('td:nth-child(1) .db-table__txt--type a:nth-child(1)').text(),
                category: $dom.find('td:nth-child(1) .db-table__txt--type a:nth-child(2)').text(),
                item_level: parseInt($dom.find('td:nth-child(2)').text()),
                required_level: parseInt($dom.find('td:nth-child(3)').text()),
            });
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim().replace(/,/g, '')),
                end: parseInt($('.current_list .show_end').eq(0).text().trim().replace(/,/g, '')),
                total: parseInt($('.current_list .total').eq(0).text().trim().replace(/,/g, '')),
            },
            version: $('.db-content__title--version').text().split(':')[1].trim(),
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },

    getData: function($) {
        // detail box
        var $dom = $('.db_cnts');

        // flags (convertable, etc)
        var flags = [];
        $dom.find('.db-view__item-info__list li').each(function() {
            flags.push($(this).find('span').text());
        });

        // selling price
        var $sells = $dom.find('.db-view__sells'),
            sale = $sells.length > 0 ? $sells.parent().text().split(' ') : 0;

        // item data
        var data = {
            icon: $dom.find('.db-view__item__icon .db-view__item__icon__item_image').attr('src'),
            color: $dom.find('.db-view__item__text__name').attr('class').split(' ')[1].trim().replace('txt-rarity_', ''),
            name: $dom.find('.db-view__item__text__name').text().trim(),
            category: $dom.find('.db-view__item__text__category').text().trim(),

            is_unique: $dom.find('.db-view__item__text__element .rare').text().trim().length > 0 ? 1 : 0,
            is_untradable: $dom.find('.db-view__item__text__element .ex_bind').text().trim().length > 0 ? 1 : 0,
            is_sellable: $dom.find('.db-view__unsellable').length > 0 ? 0 : 1,
            is_market_allowed: $dom.find('.db-view__market_notsell').length > 0 ? 0 : 1,
            is_convertible: flags[0] == 'Yes' ? 1 : 0,
            is_projectable: flags[1] == 'Yes' ? 1 : 0,
            is_desynthesizable: flags[2] == 'Yes' ? 1 : 0,
            is_dyeable: flags[3] == 'Yes' ? 1 : 0,
            is_crestable: flags[4] == 'Yes' ? 1 : 0,

            sell_nq: sale[2] ? parseInt(sale[2].replace(/,/g, ''), 10) : false,
            sell_hq: sale[5] ? parseInt(sale[5].replace(/,/g, ''), 10) : false,
        }

        // related duties
        var $footers = $('.db__l_main');
        $footers.each(function() {
            $footer = $(this);

            // related duties
            if ($footer.find('h3').text() == 'Related Duties')
            {
                data.duties = [];

                $footer.find('table tbody tr').each(function() {
                    var $column1 = $(this).find('td:nth-child(1)'),
                        $column2 = $(this).find('td:nth-child(2)'),
                        $column3 = $(this).find('td:nth-child(3)');

                    var id = $column1.find('.db-table__txt--detail_link').attr('href').split('/'),
                        id = functions.clean(id, '')[4];

                    data.duties.push({
                        id: id,
                        name: $column1.find('.db-table__txt--detail_link').text().trim(),
                        url: $column1.find('.db-table__txt--detail_link').attr('href'),
                        kind_name: $column1.find('.db-table__txt--type a:first-of-type').text(),
                        kind_url: $column1.find('.db-table__txt--type a:first-of-type').attr('href'),
                        category_name: $column1.find('.db-table__txt--type a:last-of-type').text(),
                        category_url: $column1.find('.db-table__txt--type a:last-of-type').attr('href'),
                    });
                });
            }

            /*

            These need fixing as of: 29th May 2014,
            SE Updated Lodestone DB code, all class names
            have been changed.


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

            // dropped by
            if ($footer.find('h3').text() == 'Dropped By')
            {
                data.enemies = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $row2 = $(this).find('td:nth-child(2)'),
                        $name = $row1.find('a.db_popup'),
                        $area = $row2.find('.col_center a');

                    var id = $name.attr('href').split('/'),
                        id = functions.clean(id, '')[5];

                    var area_id = $area.attr('href').split('/'),
                        area_id = functions.clean(area_id, '')[4].split('=')[2];

                    data.enemies.push({
                        id: id,
                        name: $name.text().trim(),
                        url: $name.attr('href'),
                        area_id: area_id,
                        area_name: $area.text().trim(),
                        area_url: $area.attr('href'),
                    });
                });
            }

            // gathering log
            if ($footer.find('h3').text() == 'Gathering Log')
            {
                data.gathering = [];

                $footer.find('table tbody tr').each(function() {
                    var $row1 = $(this).find('td:nth-child(1)'),
                        $row2 = $(this).find('td:nth-child(2)'),
                        $name = $row1.find('a.db_popup'),
                        $icon = $row1.find('img');

                    var id = $name.attr('href').split('/'),
                        id = functions.clean(id, '')[4];

                    data.gathering.push({
                        id: id,
                        icon: $icon.attr('src'),
                        name: $name.text().trim(),
                        url: $name.attr('href'),
                        level: $row2.text().trim(),
                    });
                });
            }

            */
        });

        return data;
    },
}

// Export it
module.exports = apiDatabaseItems;
