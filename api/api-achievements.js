var apiAchievements =
{
    // get the urls for this module
    getUrl: function(type, param1, param2) {
        var urls = {
            summary: '/lodestone/character/{param1}/achievement/'.replace('{param1}', param1),
            achievement: '/lodestone/character/{param1}/achievement/kind/{param2}/'.replace('{param1}', param1).replace('{param2}', param2),
        }

        return urls[type];
    },

    //
    // Obtain a summary of the characters achievements.
    //
    getSummary: function($) {
        var data = [];
        $box = $('#contents');
        $box.find('.achievement_list li').each(function() {
            var $node = $(this);

            var achievement = {
                id: parseInt($node.find('.ic_achievement a').attr('href').split('/')[6]),
                icon: $node.find('.ic_achievement img').attr('src').trim(),
                timestamp: parseInt($node.find('.achievement_date script').html().trim().split('(')[2].split(',')[0].trim()),
                name: $node.find('.achievement_txt a').text(),
            }

            achievement.date = new Date(achievement.timestamp * 1000).toString();

            $node.find('.achievement_date').remove();
            achievement.text = $node.find('.achievement_txt').text().trim();
            achievement.type = achievement.text.substring(0, achievement.text.indexOf('achievement')).trim();

            data.push(achievement);
        });

        return data;
    },

    //
    // Get an entire list of achievements for a specific category.
    //
    getData: function($, kind) {
        var data = [];
        $box = $('#contents');
        $box.find('.achievement_cnts li').each(function() {
            var $node = $(this),
                $timestamp = $node.find('.achievement_date');
                timestamp = $timestamp.length > 0 ? parseInt($timestamp.find('script').html().trim().split('(')[2].split(',')[0].trim()) : false;

            var achievement = {
                id: parseInt($node.find('.achievement_area_body a').attr('href').split('/')[6]),
                icon: $node.find('.ic_achievement img').attr('src').trim(),
                timestamp: timestamp,
                obtained: timestamp ? true : false,
                name: $node.find('.achievement_name').text(),
                points: parseInt($node.find('.achievement_point').text()),
                transfer: $node.find('.mark.transfer').length > 0 ? true : false,
                reward_medal: $node.find('.mark.reward_medal').length > 0 ? true : false,
                reward_item: $node.find('.mark.reward_item').length > 0 ? true : false,
            }

            achievement.date = new Date(timestamp * 1000).toString();

            // if kind, include it
            if (kind) {
                achievement.kind = kind;
            }

            data.push(achievement);
        });

        return data;
    },
}

// Export it
module.exports = apiAchievements;
