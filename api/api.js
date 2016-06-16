var cheerio = require('cheerio'),
    http = require('follow-redirects').http,
    config = require('../config'),
    functions = require('../libs/functions'),
    log = require('../libs/LoggingObject'),

    apiCharacters = require('./api-characters'),
    apiAchievements = require('./api-achievements'),
    apiLodestone = require('./api-lodestone'),
    apiFreecompany = require('./api-freecompany'),
    apiLinkshell = require('./api-linkshell'),
    apiStandings = require('./api-standings'),
    apiForums = require('./api-forums'),

    apiDatabaseItems = require('./api-database-items'),
    apiDatabaseRecipes = require('./api-database-recipes'),
    apiDatabaseDuty = require('./api-database-duty');

// Update sockets
http.globalAgent.maxSockets = config.agentMaxSockets;

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

        // create path
        var urlPath = url.replace(' ', '+');
        if (config.settings.breakCacheOnHttpGet) {
            urlPath = urlPath + '#' + Date.now();
        }

        // options
        var options = {
            host: host,
            port: 80,
            path: urlPath,
        }

        // get
        var html = '',
            start = +new Date(),
            memoryStart = functions.memory();

        log.echo('{method:green}: [{language:cyan}] --> {url:cyan}', {
            method: 'GET',
            language: api.language,
            url: (options.host + options.path),
        });

        global.ANALYTICS.record('api', 'New HTTP Request: '+ urlPath);

        // request
        http.get(options, function(res) {
            res.on('data', function(data) {
                html += data;
            })
            .on('end', function() {
                // end time
                var end = +new Date(),
                    duration = (end - parseInt(start)),
                    memoryFinish = functions.memory();

                global.ANALYTICS.record('api', 'HTTP Request Completed, Duration: '+ duration + 'ms - ' + urlPath);
                global.ANALYTICS.count('api', urlPath);

                log.echo('{arrows:green} {path:yellow} - Duration: {duration:cyan} ms | Memory: {start:cyan} to {finish:cyan}', {
                    arrows: '>>',
                    path: options.path,
                    duration: duration.toString(),
                    start: functions.memoryToHuman(memoryStart),
                    finish: functions.memoryToHuman(memoryFinish),
                });
                log.space();

                // callback with a cheerio assigned html
                callback(cheerio.load(html));
            });
        });
    },

    //
    // Super simple "get and parse",
    // grouping two calls to one
    //
    getAndParse: function(url, parser, callback, extra)
    {
        // Get the page
        api.get(url, function($) {
            // run it through the parser
            parser($, callback, extra);
        });
    },

    //
    // Set the language for lodestone
    //
    setLanguage: function(lang) {
        if (lang == 'en' || lang == 'de' || lang == 'fr' || lang == 'jp') {
            api.language = lang;
        }
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // search stuff
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // search for a character
    //
    searchCharacter: function(reply, options, callback) {
        api.get(apiCharacters.getUrl('search', options.name, options.server, options.page), function($) {
            // parse search
            var data = apiCharacters.getSearch($);

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    //
    // search for an item
    //
    searchItem: function(reply, options) {
        api.get(apiDatabaseItems.getUrl('search', options.name, options.page), function($) {
            reply(apiDatabaseItems.getSearch($));
        });
    },

    //
    // Search for a recipe
    //
    searchRecipe: function(reply, options) {
        api.get(apiDatabaseRecipes.getUrl('search', options.name, options.page), function($) {
            reply(apiDatabaseRecipes.getSearch($));
        });
    },

    //
    // search for an duty
    //
    searchDuty: function(reply, options) {
        api.get(apiDatabaseDuty.getUrl('search', options.name, options.page), function($) {
            reply(apiDatabaseDuty.getSearch($));
        });
    },

    //
    // search for a freecompany
    //
    searchFreecompany: function(reply, options, callback) {
        api.get(apiFreecompany.getUrl('search', options.name, options.server), function($) {
            // parse search
            var data = apiFreecompany.getSearch($);

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    //
    // search for a linkshell
    //
    searchLinkshell: function(reply, options, callback) {
        api.get(apiLinkshell.getUrl('search', options.name, options.server), function($) {
            // parse search
            var data = apiLinkshell.getSearch($);

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Database
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // Get item data
    //
    getItem: function(reply, options) {
        api.get(apiDatabaseItems.getUrl('item', options.id), function($) {
            reply(apiDatabaseItems.getData($));
        });
    },

    //
    // Get recipe data
    //
    getRecipe: function(reply, options) {
        api.get(apiDatabaseRecipes.getUrl('recipe', options.id), function($) {
            reply(apiDatabaseRecipes.getData($));
        });
    },

    //
    // Get duty data
    //
    getDuty: function(reply, options) {
        api.get(apiDatabaseDuty.getUrl('duty', options.id), function($) {
            reply(apiDatabaseDuty.getData($));
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Character
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getCharacter: function(reply, options, callback) {
        api.get(apiCharacters.getUrl('character', options.id), function($) {
            // Parse character data
            var data = apiCharacters.getData($, options);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Character does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    getAchievementSummary: function(reply, options, callback) {
        api.get(apiAchievements.getUrl('summary', options.id), function($) {
            // Parse achievement data
            var data = apiAchievements.getSummary($, options);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Achievements does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    getAchievements: function(reply, options, callback) {
        api.get(apiAchievements.getUrl('achievement', options.id, options.kind), function($) {
            // Parse achievement data (for a specific category)
            var data = apiAchievements.getData($, options.kind);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Achievements does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    getAchievementsAll: function(reply, options, callback) {
        api.getAchievementsAllRecurrsive([1, 2, 4, 5, 6, 8, 11, 12, 13], {}, options, reply, callback);
    },

    getAchievementsAllRecurrsive: function(list, data, options, reply, callback) {
        var kind = list[0];
        list.splice(list.indexOf(kind), 1);

        api.get(apiAchievements.getUrl('achievement', options.id, kind), function($) {
            data[kind] = apiAchievements.getData($, kind);
            if (!data[kind]) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Achievements does not exist.' });
            }

            // if still more categories to get, continue onto the next one.
            if (list.length > 0) {
                api.getAchievementsAllRecurrsive(list, data, options, reply, callback)
            } else {
                // always send of reply first so the user
                // gets their response asap.
                if (reply) {
                    reply(data);
                }

                // run any callbacks
                if (callback) {
                    callback(data);
                }
            }
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Linkshells
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getLinkshell: function(reply, options, callback) {
        api.get(apiLinkshell.getUrl('linkshell', options.id), function($) {
            // Parse character data
            var data = apiLinkshell.getData($, options);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Linkshell does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Free companies
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getFreecompany: function(reply, options, callback) {
        api.get(apiFreecompany.getUrl('freecompany', options.id), function($) {
            // Parse character data
            var data = apiFreecompany.getData($, options);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Free Company does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    getFreecompanyMembers: function(reply, options, callback) {
        api.get(apiFreecompany.getUrl('members', options.id, options.page), function($) {
            // get members
            var data = apiFreecompany.getMembers($, options);
            if (!data) {
                if (callback) {
                    callback(false);
                }
                return reply({ error: 'Free Company does not exist.' });
            }

            // always send of reply first so the user
            // gets their response asap.
            if (reply) {
                reply(data);
            }

            // run any callbacks
            if (callback) {
                callback(data);
            }
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Lodestone
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    getLodestoneSlidingBanners: function(reply, options) {
        api.get(apiLodestone.getUrl('home'), function($) {
            reply(apiLodestone.getSlidingBanners($));
        });
    },

    getLodestoneTopics: function(reply, options) {
        api.get(apiLodestone.getUrl('topics'), function($) {
            reply(apiLodestone.getTopics($));
        });
    },

    getLodestoneNotices: function(reply, options) {
        api.get(apiLodestone.getUrl('notices'), function($) {
            reply(apiLodestone.getNotices($));
        });
    },

    getLodestoneMaintenance: function(reply, options) {
        api.get(apiLodestone.getUrl('maintenance'), function($) {
            reply(apiLodestone.getMaintenance($));
        });
    },

    getLodestoneUpdates: function(reply, options) {
        api.get(apiLodestone.getUrl('updates'), function($) {
            reply(apiLodestone.getUpdates($));
        });
    },

    getLodestoneStatus: function(reply, options) {
        api.get(apiLodestone.getUrl('status'), function($) {
            reply(apiLodestone.getStatus($));
        });
    },

    getLodestoneCommunity: function(reply, options) {
        api.get(apiLodestone.getUrl('community'), function($) {
            reply(apiLodestone.getCommunity($));
        });
    },

    getLodestoneEvents: function(reply, options) {
        api.get(apiLodestone.getUrl('events'), function($) {
            // get events url
            apiLodestone.getEventsUrl($, function(url) {
                // get events
                api.get(url, function($) {
                    // parse events
                    reply(apiLodestone.getEvents($));
                });
            });
        });
    },

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // Forums
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // Get dev tracker data
    //
    getDevTracker: function(reply, options) {
        // url and parser
        var url = apiForums.getUrl('devtracker'),
            parser = apiForums.getDevPostsCallback;

        // get dev posts
        api.getAndParse(url, parser, function(data)
        {
            var numOfPostsParsed = 0;

            // loop through dev posts
            for(var postId in data)
            {
                // url and parser
                var url = data[postId].post.url.replace('http://forum.square-enix.com/ffxiv/', ''),
                    url = '{forums}/ffxiv/'+ url,
                    parser = apiForums.getDevPostData;

                // get full post
                api.getAndParse(url, parser, function(post, extra)
                {
                    // get post id from the extra passed back
                    var postId = extra.id;

                    // populate the data using the post id
                    data[postId].post.message = post.message;
                    data[postId].user.name = post.user;
                    data[postId].user.color = post.color;
                    data[postId].user.avatar = post.avatar;
                    data[postId].user.title = post.title;

                    // increment numOfPostsParsed
                    numOfPostsParsed++;

                    // if numOfPostsParsed
                    if (numOfPostsParsed == functions.objLength(data))
                    {
                        // return to browser
                        reply(data);
                    }
                }, {
                    id: postId
                });
            }
        });
    },

    //
    // Get popular posts from the forums
    //
    getPopularPosts: function(reply, options) {
        api.get(apiForums.getUrl('forums'), function($) {
            reply(apiForums.getPopularPosts($));
        });
    },
}

// Export it
module.exports = api;
