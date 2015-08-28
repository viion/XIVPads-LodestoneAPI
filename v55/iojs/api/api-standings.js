var apiStandings =
{
    // set the lodestone language
    setLodestoneLanguage: function(lang) {
        if (typeof config !== 'undefined') {
            config.setLodestoneLanguage(lang);
        }
    },

    getUrl: function(type, string) {

        var urls =
        {
            frontline_weekly: '/lodestone/ranking/frontline/weekly/',
            frontline_monthly: '/lodestone/ranking/frontline/monthly/',
        }

        return urls[type];
    },

    // get frontline standing
    getFrontlineStanding: function($)
    {
        var data = [];
        $('.list_ranking li').each(function() {
            $node = $(this);

            var character = {
                rank_up: $node.find('.rank').hasClass('arrow_up'),
                id: $node.find('.userdata .thumb_cont_black a').attr('href').split('/')[3],
                avatar: $node.find('.userdata .thumb_cont_black img').attr('src'),
                name: $node.find('.player_name_gold a').text().trim(),
                server: $node.find('.world_name').text().replace('(', '').replace(')', '').trim(),
                pvp_rank: parseInt($node.find('.pvp_rank').text().split(':')[1].trim()),
                victories: parseInt($node.find('.list_body .box').eq(0).text().trim()),
                victory_rate: parseFloat($node.find('.list_body .box').eq(1).text().trim()),
                matches: parseInt($node.find('.list_body .box').eq(2).text().trim()),
            }

            // top 3
            if ($node.hasClass('rank_crown')) {
                character.rank = $node.hasClass('.rank_crown_1') ? 1 : $node.hasClass('.rank_crown_2') ? 2 : 3;
            } else {
                character.rank = parseInt($node.find('.rank div').text().trim());
            }

            if ($node.find('.ic_gcrank img').length > 0) {
                character.grand_company = {
                    icon: $node.find('.ic_gcrank img').attr('src').trim(),
                    name: $node.find('.ic_gcrank div:last-child').text().trim(),
                }
            }

            data.push(character);
        });

        return data;
    },

    // get grand company standings
    getGrandCompanyStanding: function($)
    {
        var data = [];
        $('.list_ranking li').each(function() {
            $node = $(this);

            var character = {
                rank_up: $node.find('.rank').hasClass('arrow_up'),
                id: $node.find('.userdata .thumb_cont_black a').attr('href').split('/')[3],
                avatar: $node.find('.userdata .thumb_cont_black img').attr('src'),
                name: $node.find('.player_name_gold a').text().trim(),
                server: $node.find('.player_name_gold span').text().replace('(', '').replace(')', '').trim(),
                seals: parseInt($node.find('.point_rank').text().trim()),
            }

            if ($node.find('.ic_gcrank img').length > 0) {
                character.grand_company = {
                    icon: $node.find('.ic_gcrank img').attr('src').trim(),
                    name: $node.find('.ic_gcrank div:last-child').text().split('/')[0].trim(),
                    rank: $node.find('.ic_gcrank div:last-child').text().split('/')[1].trim(),
                }
            }

            data.push(character);
        });

        return data;
    },

    getFreecompanyStanding: function($)
    {
        var data = [];
        $('.list_ranking li').each(function() {
            $node = $(this);

            var character = {
                rank_up: $node.find('.rank').hasClass('arrow_up'),
                id: $node.find('.userdata .thumb_cont_black a').attr('href').split('/')[3],
                emblums: {
                    1: $node.find('.userdata .ic_crest_64 img').eq(0).attr('src'),
                    2: $node.find('.userdata .ic_crest_64 img').eq(1).attr('src'),
                    3: $node.find('.userdata .ic_crest_64 img').eq(2).attr('src'),
                },
                name: $node.find('.player_name_gold a').text().trim(),
                server: $node.find('.player_name_gold span').text().replace('(', '').replace(')', '').trim(),
                seals: parseInt($node.find('.point').text().trim()),
            }

            if ($node.find('.ic_gcrank img').length > 0) {
                character.grand_company = {
                    icon: $node.find('.ic_gcrank img').attr('src').trim(),
                    name: $node.find('.ic_gcrank div:last-child').text().trim(),
                }
            }

            data.push(character);
        });

        return data;
    }
}

// Export it
module.exports = apiStandings;