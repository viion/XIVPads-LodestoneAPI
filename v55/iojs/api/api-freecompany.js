Array.prototype.clean = function(deleteValue) {
    for (var i = 0; i < this.length; i++) {
        if (this[i].trim() == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

var apiFreecompany =
{
    getUrl: function(type, string, server)
    {
        var urls =
        {
            search: '/lodestone/freecompany/?q={string}&worldname={server}'.replace('{string}', string).replace('{server}', server),
            freecompany: '/lodestone/freecompany/{string}/'.replace('{string}', string),
        }

        return urls[type];
    },

    getSearch: function($)
    {


        return data;
    },

    getData: function($, options)
    {
        var data = {
            emblums: {
                1: $('.area_header .ic_crest_64 img').eq(0).attr('src').trim(),
                2: $('.area_header .ic_crest_64 img').eq(1).attr('src').trim(),
                3: $('.area_header .ic_crest_64 img').eq(2).attr('src').trim(),
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

        if ($('.area_inner_body tr').eq(10).find('td:last-child .txt_yellow').html().length > 1) {
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
}

// Export it
module.exports = apiFreecompany;