var apiForums =
{
    getUrl: function(type, param1, param2) {
        var urls = {
            forums: '{forums}/ffxiv/forum.php',
            devtracker: '{forums}/ffxiv/search.php?do=process&search_type=1&contenttypeid=1&devtrack=1&starteronly=0&showposts=1&childforums=1&forumchoice[]=619',
            support: '{forums}/ffxiv/search.php?do=process&search_type=1&contenttypeid=1&supporttrack=1&starteronly=0&showposts=1&childforums=1&forumchoice[]=619',
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
    },

    //
    // Get dev posts and run a callback
    //
    getDevPostsCallback: function($, callback) {
        var data = {};

        $('#searchbits li').each(function() {
            $node = $(this);

            var postId = $node.find('.postbody h3 a').attr('href').split('#')[1].replace(/\D/g,'');

            data[postId] = {
                timestamp: $node.find('.postdate span.date').text(),
                topic: {
                    name: $node.find('.userinfo_noavatar .contact .username_container h2 a').text(),
                    url: 'http://forum.square-enix.com/ffxiv/'+ $node.find('.userinfo_noavatar .contact .username_container h2 a').attr('href'),
                },
                post: {
                    url: 'http://forum.square-enix.com/ffxiv/'+ $node.find('.postbody h3 a').attr('href'),
                    id: postId
                },
                user: {
                    name: $node.find('.userinfo_noavatar .contact .username_container > a').text(),
                    url: 'http://forum.square-enix.com/ffxiv/'+ $node.find('.userinfo_noavatar .contact .username_container > a').attr('href'),
                },
                meta: {
                    replies: parseInt($node.find('.userinfo_extra .userstats dd').eq(0).text().replace(/\D/g,'')),
                    views: parseInt($node.find('.userinfo_extra .userstats dd').eq(1).text().replace(/\D/g,'')),
                    likes: parseInt($node.find('.postbody .postrow .content div .likeitic').text().replace(/\D/g,'')),
                }
            };
        });

        return callback(data);
    },

    //
    // Get dev post data
    //
    getDevPostData: function($, callback, extra)
    {
        var $post = $('#post_' + extra.id);

        var data = {
            message: $post.find('.postcontent').html().trim(),
            user: $post.find('.userinfo .username_container a.username strong span').text().trim(),
            color: $post.find('.userinfo .username_container a.username span').css('color'),
            avatar: 'http://forum.square-enix.com/ffxiv/'+ $post.find('.userinfo .postuseravatar img').attr('src'),
            title: $post.find('.userinfo .usertitle').text().trim(),
        }

        return callback(data, extra);
    }
}

// Export it
module.exports = apiForums;