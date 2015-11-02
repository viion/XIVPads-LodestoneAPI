var apiFreecompany =
{
    // get ths urls for this module
    getUrl: function(type, param1, param2) {
        var urls = {
            freecompany: '/lodestone/freecompany/{param1}/'.replace('{param1}', param1),
            search: '/lodestone/freecompany/?q={param1}&worldname={param2}'.replace('{param1}', param1).replace('{param2}', param2),
            members: '/lodestone/freecompany/{param1}/member/?page={param2}'.replace('{param1}', param1).replace('{param2}', param2),
        }

        return urls[type];
    },

    // get search results for a free company
    getSearch: function($) {
        var results = [];
        $('.table_elements_com_fc tr').each(function() {
            $node = $(this);

            var freecompany = {
                id: $node.find('.ic_freecompany_box a').attr('href').split('/')[3],
                emblums: {
                    1: $('.ic_crest_64 img').eq(0).attr('src').trim(),
                    2: $('.ic_crest_64 img').eq(1).attr('src').trim(),
                    3: $('.ic_crest_64 img').eq(2).attr('src').trim(),
                },
                grand_company: $node.find('.player_name_area .groundcompany_name').text().trim(),
                name: $node.find('.player_name_area h4 a').text().trim(),
                server: $node.find('.player_name_area h4 span').text().replace('(', '').replace(')', '').trim(),
            };

            results.push(freecompany);
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim()),
                end: parseInt($('.current_list .show_end').eq(0).text().trim()),
                total: parseInt($('.current_list .total').eq(0).text().trim()),
            },
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },

    // get a free company
    getData: function($, options) {
        var data = {
            emblums: {
                1: $('.area_header .ic_crest_64 img').eq(0).attr('src'),
                2: $('.area_header .ic_crest_64 img').eq(1).attr('src'),
                3: $('.area_header .ic_crest_64 img').eq(2).attr('src'),
            },
            grand_company: $('.centering_h').contents()[0].data.trim(),
            friendship: $('.friendship_color').text().trim(),
            name: $('.txt_brown').text().trim(),
            server: $('.centering_h span:last-child').text().replace('(', '').replace(')', ''),
            tag: $('.area_inner_body tr').eq(0).find('td:last-child').contents()[2].data.trim(),
            formed_timestamp: parseInt($('.area_inner_body tr').eq(1).find('td:last-child script').html().trim().split('(')[2].split(',')[0].trim()),
            active_members: parseInt($('.area_inner_body tr').eq(2).find('td:last-child').text().trim()),
            rank: parseInt($('.area_inner_body tr').eq(3).find('td:last-child').text().trim()),
            ranking: {
                weekly: $('.area_inner_body tr').eq(4).find('td:last-child').contents()[0].data.trim(),
                monthly: $('.area_inner_body tr').eq(4).find('td:last-child').contents()[2].data.trim(),
            },
            slogan: $('.area_inner_body tr').eq(5).find('td:last-child').text().trim(),
            active: $('.area_inner_body tr').eq(8).find('td:last-child').text().trim(),
            recruitment: $('.area_inner_body tr').eq(9).find('td:last-child').text().trim(),
            estate: $('.area_inner_body tr').eq(10).find('td:last-child').text().trim(),
            focus: [],
            seeking: [],
        };

        // trim icon if it exists
        for(var i in [1,2,3]) {
            var num = i+1;
            data.emblums[num] = data.emblums[num] ? data.emblums[num].trim() : data.emblums[num];
        }

        var $estate = $('.area_inner_body tr').eq(10).find('td:last-child .txt_yellow');
        if ($estate.html() && $estate.length > 1) {
            $estate = $('.area_inner_body tr').eq(10).find('td:last-child');
            data.estate = {
                name: $estate.find('.txt_yellow').text().trim(),
                address: $estate.find('p').eq(0).text().trim(),
                greetings: $estate.find('p').eq(1).text().trim(),
            }
        }

        $('.focus_icon li').each(function() {
            $node = $(this);

            data.focus.push({
                icon: $node.find('img').attr('src'),
                name: $node.find('img').attr('title'),
                enabled: !$node.hasClass('icon_off'),
            })
        });

        $('.roles_icon li').each(function() {
            $node = $(this);

            data.seeking.push({
                icon: $node.find('img').attr('src'),
                name: $node.find('img').attr('title'),
                enabled: !$node.hasClass('icon_off'),
            })
        });

        data.formed_date = new Date(data.formed_timestamp * 1000).toString();

        return data;
    },

    // get members in a free company
    getMembers: function($) {
        var results = [];
        $('.table_black_border_bottom tr').each(function() {
            $node = $(this);

            var freecompany = {
                id: parseInt($node.find('.thumb_cont_black_50 a').attr('href').split('/')[3]),
                avatar: $node.find('.thumb_cont_black_50 img').attr('src').trim(),
                name: $node.find('.player_name_area h4 a').text().trim(),
                server: $node.find('.player_name_area span').text().replace('(', '').replace(')', '').trim(),
                rank: {
                    icon: $node.find('.fc_member_status img').attr('src').trim(),
                    name: $node.find('.fc_member_status').text().trim(),
                },
                class: {
                    icon: $node.find('.message_ic_box .ic_class img').attr('src').trim(),
                    level: parseInt($node.find('.message_ic_box .ic_class .lv_class').text()),
                },
                grand_company: {
                    icon: $node.find('.ic_gc img').attr('src'),
                    name: $node.find('.ic_gc div:last-child').text().split('/')[0],
                    rank: $node.find('.ic_gc div:last-child').text().split('/')[1],
                }
            };

            results.push(freecompany);
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim()),
                end: parseInt($('.current_list .show_end').eq(0).text().trim()),
                total: parseInt($('.current_list .total').eq(0).text().trim()),
            },
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },
}

// Export it
module.exports = apiFreecompany;