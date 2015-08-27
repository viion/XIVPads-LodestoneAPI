function timezone_info_from_op(op) {
    var data;

    switch ( op.method ) {
        case 'serial':
            data = timezone_info_by_serial(op);
            break;
        case 'point':
            data = timezone_info_by_point(op);
            break;
    }

    return data;
}

var apiLodestone =
{
    getUrl: function(type, string, kind)
    {
        var urls =
        {
            home: '/lodestone/',
            topics: '/lodestone/topics/',
            notices: '/lodestone/news/category/1/',
            maintenance: '/lodestone/news/category/2/',
            updates: '/lodestone/news/category/3/',
            status: '/lodestone/news/category/4/',
            community: '/lodestone/community/',
            events: '/lodestone/event/',
        }

        return urls[type];
    },

    // The sliding banners at the top of the official site.
    getSlidingBanners: function($)
    {
        var data = [];
        $('#slider_bnr_area li').each(function() {
            $node = $(this);

            data.push({
                url: $node.find('a').attr('href').trim(),
                banner: $node.find('.bnr img').attr('src').trim(),
            });
        });

        return data;
    },

    // The topics page
    getTopics: function($)
    {
        var data = [];
        $('.area_body .topics_list li').each(function() {
            $node = $(this);

            var topic = {
                url: 'http://na.finalfantasyxiv.com' + $node.find('.topics_list_inner a').attr('href'),
                title_ranking_picup: $node.find('.topics_list_inner a').text(),
                timestamp: parseInt($node.find('.topics_list_date').html().trim().split('(')[2].split(',')[0].trim()),
                html: $node.find('.area_inner_cont').html().trim(),
                banner: $node.find('.area_inner_cont img').eq(0).attr('src').trim(),
            };

            topic.html = topic.html.replace(new RegExp('/lodestone/topics/detail/', 'g'), 'http://na.finalfantasyxiv.com/lodestone/topics/detail/');
            topic.date = new Date(topic.timestamp * 1000).toString();

            // fix up html
            var $temp = $('<div>').append($(topic.html).clone());
                $temp.find('a').eq(0).remove();
                $temp.find('a').attr('target', '_blank');
                topic.html = $temp.html().trim();

            data.push(topic);
        });

        return data;
    },

    // The notices page
    getNotices: function($)
    {
        var data = [];
        $('.area_body dl.news_list').each(function() {
            $node = $(this);

            post = {
                timestamp: parseInt($node.find('.ic_info script').html().trim().split('(')[2].split(',')[0].trim()),
                icon: 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_info.png',
                link: 'http://na.finalfantasyxiv.com' + $node.find('a').eq(0).attr('href').trim(),
                name: $node.find('a').eq(0).text().trim(),
            };

            post.date = new Date(post.timestamp * 1000).toString();
            data.push(post);
        });

        return data;
    },

    // The maintenance page
    getMaintenance: function($)
    {
        var data = [];
        $('.area_body dl.news_list').each(function() {
            $node = $(this);

            post = {
                timestamp: parseInt($node.find('.ic_maintenance script').html().trim().split('(')[2].split(',')[0].trim()),
                icon: 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_maintenance.png',
                link: 'http://na.finalfantasyxiv.com' + $node.find('a').eq(0).attr('href').trim(),
                name: $node.find('a').eq(0).text().trim(),
                tag: $node.find('.tag').text().trim(),
            };

            post.date = new Date(post.timestamp * 1000).toString();
            data.push(post);
        });

        return data;
    },

    // The updates page
    getUpdates: function($)
    {
        var data = [];
        $('.area_body dl.news_list').each(function() {
            $node = $(this);

            post = {
                timestamp: parseInt($node.find('.ic_update script').html().trim().split('(')[2].split(',')[0].trim()),
                icon: 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_update.png',
                link: 'http://na.finalfantasyxiv.com' + $node.find('a').eq(0).attr('href').trim(),
                name: $node.find('a').eq(0).text().trim(),
            };

            post.date = new Date(post.timestamp * 1000).toString();
            data.push(post);
        });

        return data;
    },

    // The status page
    getStatus: function($)
    {
        var data = [];
        $('.area_body dl.news_list').each(function() {
            $node = $(this);

            post = {
                timestamp: parseInt($node.find('.ic_obstacle script').html().trim().split('(')[2].split(',')[0].trim()),
                icon: 'http://img.finalfantasyxiv.com/lds/pc/global/images/common/ic/news_obstacle.png',
                link: 'http://na.finalfantasyxiv.com' + $node.find('a').eq(0).attr('href').trim(),
                name: $node.find('a').eq(0).text().trim(),
            };

            post.date = new Date(post.timestamp * 1000).toString();
            data.push(post);
        });

        return data;
    },

    // The community page
    getCommunity: function($)
    {
        var data = [];
        $('.area_body .wall_list li').each(function() {
            $node = $(this);

            post = {
                avatar: $node.find('.wall_list_left img').attr('src').trim(),
                id: $node.find('.wall_list_left a').attr('href').split('/')[3],
                name: $node.find('.wall_list_right a').eq(0).text().trim(),
                timestamp: parseInt($node.find('.wall_list_date script').html().trim().split('(')[2].split(',')[0].trim()),
            };

            var $temp = $node.find('.wall_list_right').clone();
                $temp.find('.wall_list_date').remove();
                $temp.find('a').attr('target', '_blank');
                post.html = $temp.html().trim();

            post.html = post.html.replace(new RegExp('/lodestone/', 'g'), 'http://na.finalfantasyxiv.com/lodestone/');
            post.date = new Date(post.timestamp * 1000).toString();
            data.push(post);
        });

        return data;
    },


    /**

    ?timezone_info={"search_term":{"method":"serial","dt_times":[1440630000,{"skip":7}]},"left_limit":{"method":"point","epoch":1427328000,"year":2015,"month":3,"date":26},"next_end":{"method":"point","epoch":1441753200,"year":2015,"month":9,"date":9},"prev_end":{"method":"point","epoch":1440543600,"year":2015,"month":8,"date":26},"next_start":{"method":"point","epoch":1441234800,"year":2015,"month":9,"date":3},"prev_start":{"method":"point","epoch":1440025200,"year":2015,"month":8,"date":20},"right_limit":{"method":"point","epoch":1448496000,"year":2015,"month":11,"date":26}}&_=1440691503280

     */
    // events page
    getEvents: function($)
    {
        var json = JSON.parse($('.require_timezone_info').attr('data-require_timezone_info')),
            url = $('.require_timezone_info').attr('data-require_uri');

        var timezone_info = {};

        $.each(json, function(key, val) {
            timezone_info[key] = timezone_info_from_op(val);
        });

        json = JSON.stringify(timezone_info);

        require('./api').get('/lodestone/event/?timezone_info=' + encodeURIComponent(json), function($) {
            return apiLodestone.getEventTimetable($);
        })
    },

    getEventTimetable: function($)
    {
        var data = [];
        $('.open-event-list .event_window_body').each(function() {
            $node = $(this);

            console.log('parsing event')

            var event = {
                status: $node.find('.event_status img').attr('alt').trim(),
                status_icon: $node.find('.event_status img').attr('src').trim(),
                world: $node.find('.status-limited_world').text().trim(),
                comments: parseInt($node.find('.comment').text().trim()),
                name: $node.find('.event_title h2 a').text().trim(),
                url: 'http://na.finalfantasyxiv.com' + $node.find('.event_title h2 a').attr('href'),
                settings: $node.find('.event_data tr').eq(1).find('td').eq(1).text().trim(),
                apply: 'http://na.finalfantasyxiv.com' + $node.find('.event_data tr').eq(3).find('td').eq(0).find('.action a').attr('href'),
                from_timestamp: parseInt($node.find('.time span:first-child .datetime').attr('data-epoch')),
                to_timestamp: parseInt($node.find('.time span:last-child .datetime').attr('data-epoch')),
                closing_timestamp: parseInt($node.find('.event_data tr').eq(1).find('td').eq(2).find('.datetime').attr('data-epoch')),
                updated_timestamp: parseInt($node.find('.date-update .datetime').attr('data-epoch')),
                created_timestamp: parseInt($node.find('.date-make .datetime').attr('data-epoch')),
                party: {
                    current: parseInt($node.find('.event_data table tr:last-child td:first-child').eq(0).find('.current').text()),
                    total: parseInt($node.find('.event_data table tr:last-child td:first-child').eq(0).find('span').eq(1).text()),
                    tanks: parseInt($node.find('.event_data table tr:last-child td').eq(1).find('.role span').text()),
                    healers: parseInt($node.find('.event_data table tr:last-child td').eq(2).find('.role span').text()),
                    dps: parseInt($node.find('.event_data table tr:last-child td').eq(3).find('.role span').text()),
                    any: parseInt($node.find('.event_data table tr:last-child td').eq(4).find('.role span').text()),
                },
                host: {
                    avatar: $node.find('.event_data .owner .thumb img').attr('src').trim(),
                    class: $node.find('.event_data .owner .classjob img').attr('title'),
                    class_icon: $node.find('.event_data .owner .classjob img').attr('src'),
                    name: $node.find('.event_data .owner p:last-child a').text().trim(),
                    id: parseInt($node.find('.event_data .owner p:last-child a').attr('href').split('/')[3]),
                    server: $node.find('.event_data .owner p:last-child').text().split('(')[1].replace(')', '').trim(),
                },
                language: $node.find('.language').text().trim(),
                tags: [],
            };

            // tags
            $node.find('.tag_body a').each(function() {
                $n = $(this);

                event.tags.push({
                    name: $n.text(),
                    link: $n.attr('href'),
                });
            });

            // dates
            event.from_date = new Date(event.from_timestamp * 1000).toString();
            event.to_date = new Date(event.to_timestamp * 1000).toString();
            event.closing_date = new Date(event.closing_timestamp * 1000).toString();
            event.updated_date = new Date(event.updated_timestamp * 1000).toString();
            event.created_date = new Date(event.created_timestamp * 1000).toString();

            data.push(event);
        });

        return data;
    },

}

// Export it
module.exports = apiLodestone;