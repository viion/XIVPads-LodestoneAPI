var cheerio = require('cheerio'),
    http = require('http'),
    functions = require('../functions'),
    config = require('../config'),
    apiItems = require('./api-items'),
    apiCharacters = require('./api-characters'),
    apiAchievements = require('./api-achievements'),
    apiLodestone = require('./api-lodestone'),
    apiFreecompany = require('./api-freecompany'),
    apiLinkshell = require('./api-linkshell'),
    apiStandings = require('./api-standings'),
    apiForums = require('./api-forums');

// - - - - - - - - - - - - - - - - - - - -
// Lodestone API
// - - - - - - - - - - - - - - - - - - - -

var api = {
    language: 'na',

    /**
     * Get html from a web page
     *
     * @param url - url for options for http.get
     * @param reply - function to callback on
     */
    get: function(url, callback) {
        // set language
        config.setLodestoneLanguage(api.language);

        // lodestone url
        var host = config.lodestoneUrl;
        if (url.indexOf('{forums}') > -1) {
            url = url.replace('{forums}', '');
            host = config.forumsUrl;
        }

        // options
        var options = {
            host: host,
            port: 80,
            path: url.replace(' ', '+'),
        }

        // get
        var html = '',
            start = +new Date(),
            memoryStart = functions.memory();

        console.log('- Language:' + api.language);
        console.log('- URL: ' + options.host + options.path);
        console.log('- Start:', start);

        // request
        http.get(options, function(res) {
            res.on('data', function(data) {
                html += data;
            })
            .on('end', function() {
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

    // Set the language for lodestone
    setLanguage: function(lang) {
        if (lang == 'en' || lang == 'de' || lang == 'fr' || lang == 'jp') {
            api.language = lang;
        }
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // search stuff
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    // search for a character
    searchCharacter: function(reply, options) {
        console.log('- searchCharacter', options);
        api.get(apiCharacters.getUrl('search', options.name, options.server), function($) {
            reply(apiCharacters.getSearch($)).header('Content-Type', 'application/json');
        });
    },

    // search for an item
    searchItem: function(reply, options) {
        console.log('- searchItem', options);
        api.get(apiItems.getUrl('search', options.name), function($) {
            reply(apiItems.getSearch($)).header('Content-Type', 'application/json');
        });
    },

    // search for a freecompany
    searchFreecompany: function(reply, options) {
        console.log('- searchFreecompany', options);
        api.get(apiFreecompany.getUrl('search', options.name, options.server), function($) {
            reply(apiFreecompany.getSearch($)).header('Content-Type', 'application/json');
        });
    },

    // search for a linkshell
    searchLinkshell: function(reply, options) {
        console.log('- searchLinkshell', options);
        api.get(apiLinkshell.getUrl('search', options.name, options.server), function($) {
            reply(apiLinkshell.getSearch($)).header('Content-Type', 'application/json');
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Database
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getItem: function(reply, options) {
        console.log('Getting item for id:', id);
        api.get(apiItems.getUrl('item', options.id), function($) {
            reply(apiItems.getData($)).header('Content-Type', 'application/json');
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Character
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getCharacter: function(reply, options) {
        console.log('- getCharacter', options);
        api.get(apiCharacters.getUrl('character', options.id), function($) {
            reply(apiCharacters.getData($, options)).header('Content-Type', 'application/json');
        });
    },

    getAchievementSummary: function(reply, options) {
        console.log('- getAchievementSummary', options);
        api.get(apiAchievements.getUrl('summary', options.id), function($) {
            reply(apiAchievements.getSummary($)).header('Content-Type', 'application/json');
        });
    },

    getAchievements: function(reply, options) {
        console.log('- getAchievements', options);
        api.get(apiAchievements.getUrl('achievement', options.id, options.kind), function($) {
            reply(apiAchievements.getData($)).header('Content-Type', 'application/json');
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Linkshells
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getLinkshell: function(reply, options) {
        console.log('- getLinkshell', options);
        api.get(apiLinkshell.getUrl('linkshell', options.id), function($) {
            reply(apiLinkshell.getData($, options)).header('Content-Type', 'application/json');
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Free companies
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getFreecompany: function(reply, options) {
        console.log('- getFreecompany', options);
        api.get(apiFreecompany.getUrl('freecompany', options.id), function($) {
            reply(apiFreecompany.getData($, options)).header('Content-Type', 'application/json');
        });
    },

    getFreecompanyMembers: function(reply, options) {
        console.log('- getFreecompanyMembers', options);
        api.get(apiFreecompany.getUrl('getMembers', options.id, options.page), function($) {
            reply(apiFreecompany.getData($, options)).header('Content-Type', 'application/json');
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Lodestone
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getLodestoneSlidingBanners: function(reply, options) {
        console.log('- getLodestoneSlidingBanners', options);
        api.get(apiLodestone.getUrl('home'), function($) {
            reply(apiLodestone.getSlidingBanners($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneTopics: function(reply, options) {
        console.log('- getLodestoneTopics', options);
        api.get(apiLodestone.getUrl('topics'), function($) {
            reply(apiLodestone.getTopics($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneNotices: function(reply, options) {
        console.log('- getLodestoneNotices', options);
        api.get(apiLodestone.getUrl('notices'), function($) {
            reply(apiLodestone.getNotices($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneMaintenance: function(reply, options) {
        console.log('- getLodestoneMaintenance', options);
        api.get(apiLodestone.getUrl('maintenance'), function($) {
            reply(apiLodestone.getMaintenance($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneUpdates: function(reply, options) {
        console.log('- getLodestoneUpdates', options);
        api.get(apiLodestone.getUrl('updates'), function($) {
            reply(apiLodestone.getUpdates($));.header('Content-Type', 'application/json');
        });
    },

    getLodestoneStatus: function(reply, options) {
        console.log('- getLodestoneStatus', options);
        api.get(apiLodestone.getUrl('status'), function($) {
            reply(apiLodestone.getStatus($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneCommunity: function(reply, options) {
        console.log('- getLodestoneCommunity', options);
        api.get(apiLodestone.getUrl('community'), function($) {
            reply(apiLodestone.getCommunity($)).header('Content-Type', 'application/json');
        });
    },

    getLodestoneEvents: function(reply, options) {
        console.log('- getLodestoneEvents', options);
        api.get(apiLodestone.getUrl('events'), function($) {
            // get events url
            apiLodestone.getEventsUrl($, function(url) {
                // get events
                api.get(url, function($) {
                    // parse events
                    reply(apiLodestone.getEvents($)).header('Content-Type', 'application/json');
                });
            });
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Forums
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getDevTracker: function(reply, options) {
        console.log('- getDevTracker', options);
        api.get(apiForums.getUrl('forums'), function($) {
            reply(apiForums.getDevTracking($)).header('Content-Type', 'application/json');
        });
    },

    getPopularPosts: function(reply, options) {
        console.log('- getPopularPosts', options);
        api.get(apiForums.getUrl('forums'), function($) {
            reply(apiForums.getPopularPosts($)).header('Content-Type', 'application/json');
        });
    },
}

// Export it
module.exports = api;