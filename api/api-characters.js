var functions = require('../functions');

var apiCharacters =
{
    // get thr urls for this module
    getUrl: function(type, param1, param2, page) {
        var urls = {
            character: '/lodestone/character/{param1}/'.replace('{param1}', param1),
            search: '/lodestone/character/?q={param1}&worldname={param2}&page={page}'.replace('{param1}', param1).replace('{param2}', param2).replace('{page}', page),
        }

        return urls[type];
    },

    getSearch: function($) {
        var results = [];
        $('.table_black_border_bottom table tr').each(function() {
            $node = $(this);

            var character = {
                id: parseInt($node.find('.thumb_cont_black_50 a').attr('href').split('/')[3]),
                avatar: $node.find('.thumb_cont_black_50 img').attr('src').trim(),
                name: $node.find('.player_name_gold a').text().trim(),
                server: $node.find('.player_name_gold span').text().replace(/[^a-zA-Z]+/g, '').trim(),
                language: $node.find('.right_txt').text().trim(),
                active_class: {
                    icon: $node.find('.col3box .col3box_left img').attr('src'),
                    level: parseInt($node.find('.col3box .col3box_left div').eq(1).text()),
                },
                grand_company: false,
            }

            // grand company
            if ($node.find('.col3box .col3box_center').text().trim().length > 0) {
                character.grand_company = {
                    icon: $node.find('.col3box .col3box_center').find('img').attr('src'),
                    name: $node.find('.col3box .col3box_center').find('div').eq(1).text().split('/')[0],
                    rank: $node.find('.col3box .col3box_center').find('div').eq(1).text().split('/')[1],
                }
            }

            // free company
            if ($node.find('.col3box .col3box_right .ic_gc').html().trim().length > 0) {
                $fc = $node.find('.col3box .col3box_right');
                character.free_company = {
                    id: $fc.find('.txt_gc a').attr('href').split('/')[3],
                    name: $fc.find('.txt_gc').text().trim(),
                    icon: {
                        1: $fc.find('.ic_crest_32 img').eq(0).attr('src').replace('40x40', '64x64'),
                        2: $fc.find('.ic_crest_32 img').eq(0).attr('src').replace('40x40', '64x64'),
                        3: $fc.find('.ic_crest_32 img').eq(0).attr('src').replace('40x40', '64x64'),
                    }
                }
            }

            results.push(character);
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim().replace(/,/g, '')),
                end: parseInt($('.current_list .show_end').eq(0).text().trim().replace(/,/g, '')),
                total: parseInt($('.current_list .total').eq(0).text().trim().replace(/,/g, '')),
            },
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },

    getData: function($, options) {
        // detail box
        var $box = $('#character');

        var id = parseInt($box.find('.player_name_txt .player_name_thumb a').attr('href').trim().match(/\d+/)[0]),
            body = $box.find('.chara_profile_left .chara_profile_box .chara_profile_title').text().split('/'),
            race = body[0].trim(),
            clan = body[1].trim(),
            gender = (body[2].trim() == '♀') ? 'female' : 'male',
            $profile = $box.find('.chara_profile_left .chara_profile_footer dl');

        // active class name
        var activeClassName = $box.find('#param_class_info_area').eq(0).find('.item_detail_box .item_name_right .category_name').text(),
            activeClassName = activeClassName.split("'")[0],
            activeClassName = activeClassName.replace('Two-Handed', ''),
            activeClassName = activeClassName.replace('One--Handed', '');

        // data
        var data =
        {
            id: id,
            name: $box.find('.player_name_txt h2 a').text().trim(),
            server: $box.find('.player_name_txt h2 span').text().trim().replace(/[^a-zA-Z]+/g, ''),
            title: $box.find('.player_name_txt h2 .chara_title').text().trim(),
            avatar: $box.find('.player_name_txt .player_name_thumb img').attr('src').trim(),
            portrait: $box.find('#chara_img_area .bg_chara_264 img').attr('src').trim(),
            biography: $box.find('.txt_selfintroduction').text().trim(),
            race: race,
            clan: clan,
            gender: gender,
            nameday: $profile.eq(0).find('dd').eq(1).text(),
            guardian: {
                icon: $profile.eq(0).find('img').attr('src'),
                name: $profile.eq(0).find('dd').eq(3).text(),
            },
            city: {
                icon: $profile.eq(1).find('img').attr('src'),
                name: $profile.eq(1).find('dd').eq(1).text(),
            },
            grand_company: {},
            free_company: {},
            classjobs: {},
            stats: {
                core: {},
                attributes: {},
                elemental: {},
                properties: {},
                resistances: {},
            },
            mounts: [],
            minions: [],
            active_class: {
                level: parseInt($box.find('#param_class_info_area').eq(0).find('#class_info .level').text().match(/\d+/)[0]),
                icon: $box.find('#param_class_info_area').eq(0).find('#class_info img').eq(0).attr('src').trim(),
                name: activeClassName,
            },
            active_gear: {},
        }

        // grand company
        if ($profile.eq(2).find('img').attr('src'))
        {
            data.grand_company = {
                icon: $profile.eq(2).find('img').attr('src'),
                name: $profile.eq(2).find('dd').eq(1).text().split('/')[0],
                rank: $profile.eq(2).find('dd').eq(1).text().split('/')[1],
            };
        }

        // free company
        if ($profile.eq(3).find('a').attr('href'))
        {
            data.free_company = {
                id: $profile.eq(3).find('a').attr('href').match(/\d+/)[0],
                name: $profile.eq(3).find('dd').eq(1).text(),
                icon: {
                    1: $profile.eq(3).find('img').eq(0).attr('src').replace('40x40', '64x64'),
                    2: $profile.eq(3).find('img').eq(1).attr('src').replace('40x40', '64x64'),
                    3: $profile.eq(3).find('img').eq(2).attr('src').replace('40x40', '64x64'),
                },
            };
        }

        // class jobs
        $box.find('.base_body .class_list').each(function()
        {
            $(this).find('tr').each(function()
            {
                // left column
                var level = $(this).find('td').eq(1).text(),
                    level = (level == '-') ? 0 : parseInt(level),

                    current = $(this).find('td').eq(2).text().split('/')[0].trim(),
                    current = (current == '-') ? 0 : parseInt(current),

                    max = $(this).find('td').eq(2).text().split('/')[1].trim(),
                    max = (max == '-') ? 0 : parseInt(max),

                    name = $(this).find('td').eq(0).text();

                // push entry
                data.classjobs[name.toLowerCase()] = {
                    icon: $(this).find('td').eq(0).find('img').attr('src'),
                    name: name,
                    level: level,
                    exp: {
                        'current': current,
                        'max': max,
                    }
                };

                // right column
                if ($(this).find('td').eq(4).text())
                {
                    var level = $(this).find('td').eq(4).text(),
                        level = (level == '-') ? 0 : parseInt(level),

                        current = $(this).find('td').eq(5).text().split('/')[0].trim(),
                        current = (current == '-') ? 0 : parseInt(current),

                        max = $(this).find('td').eq(5).text().split('/')[1].trim(),
                        max = (max == '-') ? 0 : parseInt(max),

                        name = $(this).find('td').eq(3).text();

                    // push entry
                    data.classjobs[name.toLowerCase()] = {
                        icon: $(this).find('td').eq(3).find('img').attr('src'),
                        name: name,
                        level: level,
                        exp: {
                            'current': current,
                            'max': max,
                        }
                    };
                }
            });
        });

        // stats - core
        $box.find('#power_gauge li').each(function()
        {
            var attribute = $(this).attr('class').trim(),
                value = parseInt($(this).text().trim());

            data.stats.core[attribute.toLowerCase()] = {
                name: attribute.toUpperCase(),
                value: value,
            };
        });

        // stats - attributes
        $box.find('.param_left_area .param_list_attributes li').each(function()
        {
            var attribute = $(this).find('img').attr('src').split('_')[1].split('.')[0],
                help = $(this).find('img').attr('title'),
                value = parseInt($(this).find('.right').text());

            switch(attribute)
            {
                case 'str': attribute = 'strength'; break;
                case 'dex': attribute = 'dexterity'; break;
                case 'vit': attribute = 'vitality'; break;
                case 'int': attribute = 'intelligence'; break;
                case 'mnd': attribute = 'mind'; break;
                case 'pie': attribute = 'piety'; break;
            }

            data.stats.attributes[attribute.toLowerCase()] = {
                name: attribute,
                //help: help,
                value: value,
            };
        });

        // stats - elemental
        $box.find('.param_left_area .param_list_elemental li').each(function()
        {
            var attribute = $(this).find('.help_text').text().trim(),
                shortname = attribute.toLowerCase().replace(new RegExp(' ', 'g'), '_');
                help = $(this).find('.help_text').attr('title').trim(),
                icon = $(this).find('.help_text img').attr('src').trim(),
                value = parseInt($(this).find('.val').text().trim());


            data.stats.elemental[shortname] = {
                name: attribute,
                //help: help,
                value: value,
                icon: icon,
            };
        });

        // stats - properties
        $box.find('.param_left_area .param_list').each(function()
        {
            $(this).find('li').each(function()
            {
                var attribute = $(this).find('.help_text').text().trim(),
                    shortname = attribute.toLowerCase().replace(new RegExp(' ', 'g'), '_');
                    help = $(this).find('.help_text').attr('title').trim(),
                    value = parseInt($(this).find('.right').text().trim());

                data.stats.properties[shortname] = {
                    name: attribute,
                    //help: help,
                    value: value,
                };
            });
        });

        // stats - resistances
        $box.find('.resistances .param_list li').each(function()
        {
            var attribute = $(this).find('.help_text').text().trim(),
                shortname = attribute.toLowerCase().replace(new RegExp(' ', 'g'), '_');
                help = $(this).find('.help_text').attr('title').trim(),
                value = parseInt($(this).find('.right').text().trim());

            data.stats.resistances[shortname] = {
                name: attribute,
                //help: help,
                value: value,
            };
        });

        // mounts
        $box.find('.minion_box').eq(0).find('a').each(function()
        {
            data.mounts.push({
                name: $(this).attr('title'),
                icon: $(this).find('img').attr('src').trim(),
            });
        });

        // minions
        $box.find('.minion_box').eq(1).find('a').each(function()
        {
            data.minions.push({
                name: $(this).attr('title'),
                icon: $(this).find('img').attr('src').trim(),
            });
        });

        // gear
        $box.find('.contents').eq(0).find('.item_detail_box').each(function(i)
        {
            var $item = $(this),
                gearLevel = $item.find('.db-tooltip__item_equipment__level').text();
                gearLevel = (gearLevel) ? parseInt(gearLevel.match(/\d+/)[0]) : 0;

            var color = $item.find('h2.db-tooltip__item__name').attr('class').trim().split(' ');
                color = functions.clean(color, '')[1].split('_')[0].trim().replace('txt-', '');

            var gear =
            {
                id: $item.find('.db-tooltip__bt_item_detail a').attr('href').split('/')[5].trim(),
                icon: $item.find('.db-tooltip__item__icon img').eq(1).attr('src'),
                name: $item.find('h2.db-tooltip__item__name').text().trim(),
                color: color,
                category: $item.find('.db-tooltip__item__category').text(),
                item_level: parseInt($item.find('.db-tooltip__item__level').text().match(/\d+/)[0]),
                gear_level: gearLevel,
                gear_classjob: $item.find('.db-tooltip__item_equipment__class').text(),
                slot: null,
                stats: {},
            };

            // slot, special conditions for main and ring
            slot = (i == 0) ? 'main-hand' : gear.category.toLowerCase();
            slot = (slot.indexOf('secondary tool') > -1) ? 'off-hand' : slot;
            slot = (slot == 'ring' && data.active_gear.ring1) ? 'ring2' : (slot == 'ring') ? 'ring1' : slot;
            slot = (slot == 'shield') ? 'off-hand' : slot;

            gear.slot = slot;
            shortslot = slot.replace(new RegExp(' ', 'g'), '_');

            // item stats - core
            $item.find('.db-db-tooltip__item_spec > div').eq(2).find('.db-tooltip__item_spec__value').each(function(i) {
                $node = $(this);
                $valueNode = $item.find('.db-tooltip__item_spec').eq(1).find('.db-tooltip__item_spec__name').eq(i);

                var name = $node.text().trim(),
                    shortname = name.toLowerCase().replace(new RegExp(' ', 'g'), '_');
                    value = parseFloat($valueNode.text().trim());

                gear.stats[shortname] = {
                    name: name,
                    value: value,
                };
            });

            // item stats - attributes
            $item.find('.db-tooltip__basic_bonus li').each(function()
            {
                var name = $(this).text().split('+')[0].trim(),
                    shortname = name.toLowerCase().replace(new RegExp(' ', 'g'), '_');
                    value = parseInt($(this).text().split('+')[1]);

                gear.stats[shortname] = {
                    name: name,
                    value: value,
                };
            });

            console.log(gear);

            data.active_gear[shortslot] = gear;
        });


        // - - - - - - - - - - - - - - - - - - - - - - - -
        // Options
        // - - - - - - - - - - - - - - - - - - - - - - - -

        // restrict to these properties
        if (options.restrict)
        {
            var restrict = options.restrict.split(','),
                temp = {};

            for(var i in restrict) {
                var field = restrict[i];
                if (!data[field]) continue;

                temp[field] = data[field];
            }

            data = temp;
        }

        // ignore these properties
        if (options.ignore)
        {
            var ignore = options.ignore.split(',');

            for(var i in ignore) {
                var field = ignore[i];
                if (!data[field]) continue;

                delete data[field];
            }
        }

        return data;
    },
}

// Export it
module.exports = apiCharacters;
