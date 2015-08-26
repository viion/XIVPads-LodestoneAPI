var cheerio = require('cheerio'),
    http = require('http'),
    apiItems = require('./api-items'),
    apiCharacters = require('./api-characters'),
    apiAchievements = require('./api-achievements'),
    apiLodestone = require('./api-lodestone');

// - - - - - - - - - - - - - - - - - - - -
// Lodestone API
// - - - - - - - - - - - - - - - - - - - -

var api =
{
    reply: null,

    memory: function()
    {
        return process.memoryUsage().heapUsed;
    },

    memoryToHuman: function(bytes)
    {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },

    ucwords: function(string)
    {
        return (string + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
    },

    /**
     * Get html from a web page
     *
     * @param url - url for options for http.get
     * @param callback - function to callback on
     */
    get: function(url, callback)
    {
        console.log('Get Path:', url);

        var options = {
            host: 'eu.finalfantasyxiv.com',
            port: 80,
            path: url,
        }

        // get
        var html = '',
            start = +new Date(),
            memoryStart = api.memory();

        console.log('- Start:', start);

        // request
        http.get(options, function(res)
        {
            res.on('data', function(data)
            {
                html += data;
            })
            .on('end', function()
            {
                // end time
                var end = +new Date(),
                    duration = (end - start),
                    memoryFinish = api.memory();

                console.log('- End:', end);
                console.log('- Duration:', duration);
                console.log('- memoryStart:', memoryStart, api.memoryToHuman(memoryStart));
                console.log('- memoryFinish:', memoryFinish, api.memoryToHuman(memoryFinish));

                // callback with a cheerio assigned html
                callback(cheerio.load(html));
            });
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // search stuff
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    // search for a character
    searchCharacter: function(name, server)
    {
        console.log('Searching for character:', name, server);

        api.get(apiCharacters.getUrl('search', name, server), function($) {
            api.reply(apiCharacters.getSearch($));
        });
    },

    // search for an item
    searchItem: function(name)
    {
        console.log('Searching for item:', name);

        api.get(apiItems.getUrl('search', name), function($) {
            api.reply(apiItems.getSearch($));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // get stuff
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getItem: function(id)
    {
        console.log('Getting item for id:', id);

        api.get(apiItems.getUrl('item', id), function($) {
            api.reply(apiItems.getData($));
        });
    },

    getCharacter: function(id, options)
    {
        console.log('Getting character for id:', id);

        api.get(apiCharacters.getUrl('character', id), function($) {
            api.reply(apiCharacters.getData($, options));
        });
    },

    getAchievementSummary: function(id)
    {
        console.log('Getting achievements summary for id:', id);

        api.get(apiAchievements.getUrl('summary', id), function($) {
            api.reply(apiAchievements.getSummary($));
        });
    },

    getAchievements: function(id, kind)
    {
        console.log('Getting achievements for id:', id, ', kind:', kind);

        api.get(apiAchievements.getUrl('achievement', id, kind), function($) {
            api.reply(apiAchievements.getData($));
        });
    },

    getLodestoneSlidingBanners: function()
    {
        console.log('Getting lodestone sliding banners');

        api.get(apiLodestone.getUrl('home'), function($) {
            api.reply(apiLodestone.getSlidingBanners($));
        });
    },

    getLodestoneTopics: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('home'), function($) {
            api.reply(apiLodestone.getSlidingBanners($));
        });
    },
}

// Export it
module.exports = api;