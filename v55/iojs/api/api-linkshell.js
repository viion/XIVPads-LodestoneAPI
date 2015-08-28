var apiLinkshell =
{
    // set the lodestone language
    setLodestoneLanguage: function(lang) {
        if (typeof config !== 'undefined') {
            config.setLodestoneLanguage(lang);
        }
    },

    getUrl: function(type, param1, param2)
    {
        var urls =
        {
            linkshell: '/lodestone/linkshell/{param1}/?page={param2}'.replace('{param1}', param1).replace('{param2}', param2),
            search: '/lodestone/linkshell/?q={param1}&worldname={param2}'.replace('{param1}', param1).replace('{param2}', param2),
        }

        return urls[type];
    },

    // get search results
    getSearch: function($)
    {
        var results = [];
        $('.table_elements_com_ls tr').each(function() {
            $node = $(this);

            results.push({
                id: $node.find('th a').attr('href').split('/')[3].trim(),
                name: $node.find('th h4 a').text().trim(),
                server: $node.find('th h4 span').text().replace('(', '').replace(')', '').trim(),
                active_members: parseInt($node.find('td span').text().trim().replace( /^\D+/g, '')),
            });
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

    // get linkshell members
    getData: function($, options)
    {
        var results = [];
        $('.table_black_border_bottom tr').each(function() {
            $node = $(this);

            var character = {
                id: parseInt($node.find('th a').attr('href').split('/')[3]),
                avatar: $node.find('th img').attr('src').trim(),
                name: $node.find('td h4 .name_box a').text().trim(),
                server: $node.find('td h4 .name_box span').text().replace('(', '').replace(')', '').trim(),
                master: $node.find('td .ic_master').length > 0 ? true : false,
                leader: $node.find('td .ic_leader').length > 0 ? true : false,
                class: {
                    icon: $node.find('td .col3box_left img').attr('src').trim(),
                    level: parseInt($node.find('td .col3box .col3box_left div:last-child').text().trim()),
                },
                grand_company: false,
                free_company: false,
            }

            // if grand company
            if ($node.find('td .col3box_center img').length > 0) {
                character.grand_company = {
                    icon: $node.find('td .col3box_center img').attr('src').trim(),
                    name: $node.find('td .col3box_center div:last-child').text().split('/')[0],
                    rank: $node.find('td .col3box_center div:last-child').text().split('/')[1],
                };
            }

            // if free company
            if ($node.find('td .col3box_right .ic_gc img').eq(2).length > 0) {
                character.free_company = {
                    emblums: {
                        1: $node.find('td .col3box_right .ic_gc img').eq(0).attr('src').trim(),
                        2: $node.find('td .col3box_right .ic_gc img').eq(1).attr('src').trim(),
                        3: $node.find('td .col3box_right .ic_gc img').eq(2).attr('src').trim(),
                    },
                    id: $node.find('td .txt_gc a').attr('href').split('/')[3],
                    name: $node.find('td .txt_gc a').text().trim(),
                };
            }

            results.push(character);
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim()),
                end: parseInt($('.current_list .show_end').eq(0).text().trim()),
                total: parseInt($('.current_list .total').eq(0).text().trim()),
            },
            results: results,
        }

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },
}

// Export it
module.exports = apiLinkshell;