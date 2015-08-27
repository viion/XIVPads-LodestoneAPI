var cheerio = require('cheerio'),
    http = require('http'),
    functions = require('../functions'),
    apiItems = require('./api-items'),
    apiCharacters = require('./api-characters'),
    apiAchievements = require('./api-achievements'),
    apiLodestone = require('./api-lodestone'),
    apiFreecompany = require('./api-freecompany'),
    apiLinkshell = require('./api-linkshell'),
    apiStandings = require('./api-standings');

// - - - - - - - - - - - - - - - - - - - -
// Lodestone API
// - - - - - - - - - - - - - - - - - - - -

var api =
{
    reply: null,

    /**
     * Get html from a web page
     *
     * @param url - url for options for http.get
     * @param callback - function to callback on
     */
    get: function(url, callback)
    {
        var options = {
            host: 'eu.finalfantasyxiv.com',
            port: 80,
            path: url,
        }

        // get
        var html = '',
            start = +new Date(),
            memoryStart = functions.memory();

        console.log('- URL: ' + options.host + options.path);
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
                    memoryFinish = functions.memory();

                console.log('- End:', end);
                console.log('- Duration:', duration);
                console.log('- memoryStart:', memoryStart, functions.memoryToHuman(memoryStart));
                console.log('- memoryFinish:', memoryFinish, functions.memoryToHuman(memoryFinish));

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

    // search for a freecompany
    searchFreecompany: function(name, server)
    {
        console.log('Searching for freecompany:', name, server);

        api.get(apiFreecompany.getUrl('search', name, server), function($) {
            api.reply(apiFreecompany.getSearch($));
        });
    },

    // search for a linkshell
    searchLinkshell: function(name, server)
    {
        console.log('Searching for linkshell:', name, server);

        api.get(apiLinkshell.getUrl('search', name, server), function($) {
            api.reply(apiLinkshell.getSearch($));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Database
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getItem: function(id)
    {
        console.log('Getting item for id:', id);

        api.get(apiItems.getUrl('item', id), function($) {
            api.reply(apiItems.getData($));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Character
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Linkshells
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getLinkshell: function(id, options)
    {
        console.log('Getting character for id:', id);

        api.get(apiLinkshell.getUrl('linkshell', id), function($) {
            api.reply(apiLinkshell.getData($, options));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Free companies
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getFreecompany: function(id, options)
    {
        console.log('Getting freecompany for id:', id);

        api.get(apiFreecompany.getUrl('freecompany', id), function($) {
            api.reply(apiFreecompany.getData($, options));
        });
    },

    getFreecompanyMembers: function(id, options)
    {
        console.log('Getting freecompany members for id:', id);

        api.get(apiFreecompany.getUrl('getMembers', id, 1), function($) {
            api.reply(apiFreecompany.getData($, options));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Lodestone
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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

        api.get(apiLodestone.getUrl('topics'), function($) {
            api.reply(apiLodestone.getTopics($));
        });
    },

    getLodestoneNotices: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('notices'), function($) {
            api.reply(apiLodestone.getNotices($));
        });
    },

    getLodestoneMaintenance: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('maintenance'), function($) {
            api.reply(apiLodestone.getMaintenance($));
        });
    },

    getLodestoneUpdates: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('updates'), function($) {
            api.reply(apiLodestone.getUpdates($));
        });
    },

    getLodestoneStatus: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('status'), function($) {
            api.reply(apiLodestone.getStatus($));
        });
    },

    getLodestoneCommunity: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('community'), function($) {
            api.reply(apiLodestone.getCommunity($));
        });
    },

    getLodestoneEvents: function()
    {
        console.log('Getting lodestone topics');

        api.get(apiLodestone.getUrl('events'), function($) {
            api.reply(apiLodestone.getEvents($));
        });
    },
}

// Export it
module.exports = api;