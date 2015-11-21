var apiForums =
{
    getUrl: function(type, param1, param2) {
        var urls = {
            forums: '{forums}/ffxiv/forum.php',
        }

        return urls[type];
    },

    // get dev tracking
    getDevTracking: function($) {
        var data = [];
        $('#sidebar #block_newposts_ li').each(function() {
            $node = $(this);

            var url = $node.find('.comments_member_avatar_link').attr('href').trim(),
                timestamp = $node.find('.meta').text().trim();

            //
            // Timestamps are in JST, it is very difficult to convert to UTC
            // so it will be ignored and rendered directly, with "JST" appended onto it.
            //
            // Frontend visuals are best to just represent the "today" or "yesterday"
            // until the date shows up
            //

            data.push({
                member: {
                    url: 'http://forum.square-enix.com/ffxiv/' + url,
                    name: url.split('/')[1].split('-')[1].replace('_', ' ').split('?')[0],
                    id: parseInt(url.split('/')[1].split('-')[0].trim()),
                    icon: 'http://forum.square-enix.com/ffxiv/' + $node.find('.comments_member_avatar_link img').attr('src'),
                },
                message: $node.find('.widget_post_content').text(),
                title: $node.find('.widget_post_header a').text(),
                url: 'http://forum.square-enix.com/ffxiv/' +  $node.find('.widget_post_header a').attr('href').trim(),
                timestamp: timestamp,
                date: timestamp.split(' '),
            });
        });

        return data;
    },

    getPopularPosts: function($) {
        var data = [];
        $('#sidebar #block_newthreads_ li').each(function() {
            $node = $(this);

            // timestamp
            var timestamp = new Date($node.find('.meta').contents()[4].data + $node.find('.meta span').text().trim());
            timestamp = timestamp.getTime() / 1000;

            // date
            var date = new Date(timestamp * 1000).toString();

            data.push({
                title: $node.find('.widget_post_header a').text(),
                url: 'http://forum.square-enix.com/ffxiv/' + $node.find('.widget_post_header a').attr('href').trim(),
                topic: {
                    name: $node.find('.meta a').eq(1).text().trim(),
                    url: 'http://forum.square-enix.com/ffxiv/' + $node.find('.meta a').eq(1).attr('href').trim(),
                },
                user: {
                    name: $node.find('.meta a').eq(0).text().trim(),
                    url: 'http://forum.square-enix.com/ffxiv/' + $node.find('.meta a').eq(0).attr('href').trim(),
                },
                replies: parseInt($node.find('.meta').contents()[2].data.trim().match(/\d+/)[0]),
                timestamp: timestamp,
                date: date,
            });
        });

        return data;
    }
}

// Export it
module.exports = apiForums;