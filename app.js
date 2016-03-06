// Setup
var fs = require('fs'),
    api = require('./api/api'),
    config = require('./config'),
    functions = require('./functions'),
    hapi = require('hapi'),
    path = require('path');

// create server
var server = new hapi.Server();

// Server connections
server.connection({
    host: config.host,
    port: config.port
});

if (typeof config.hapi.tls !== 'undefined') {
    server.connection({
        host: config.host,
        port: config.portssl,
        tls: {
            key: fs.readFileSync(config.hapi.tls.key),
            cert: fs.readFileSync(config.hapi.tls.cert),
        }
    });
}

// Register vision
server.register(require('vision'), function (err) {
    server.views({
        engines: {
            html: require('handlebars')
        },
        relativeTo: __dirname,
        path: './views',
        isCached: false,
    });
});

// set headers before response
server.ext('onPreResponse', function(request, reply) {
    var path = request.path;

    if (typeof request.response.header === "function") {
        // check path to dermine if we need json response
        if (['/', '/characters', '/freecompany', '/linkshells', '/database', '/lodestone'].indexOf(path) == -1) {
            request.response.header('Content-Type', 'application/json');
        }

        request.response.header('Access-Control-Allow-Origin', '*');
        request.response.header('Cache-Control', 'max-age=3600');
    }

    reply(request.response);
});

// - - - - - - - - - - - - - - - - - - - - - - - - -
// Routes
//
//      /
//      /characters/search/{name}
//      /characters/get/{id}
//      /characters/get/{id}/achievements/
//      /characters/get/{id}/achievements/{kind}
//
//      /freecompany
//      /freecompany/search
//      /freecompany/get/{id}
//      /freecompany/get/{id}/members
//
//      /linkshells
//      /linkshells/search
//      /linkshells/get/{id}
//
//      /database
//
//      /forums/devtracker
//      /forums/popularposts
//
//      /lodestone
//      /lodestone/banners
//      /lodestone/topics
//      /lodestone/notices
//      /lodestone/maintenance
//      /lodestone/updates
//      /lodestone/status
//      /lodestone/community
//      /lodestone/events
//
//      /item/search/{name}
//      /item/get/{id}
//
// - - - - - - - - - - - - - - - - - - - - - - - - -

// home
server.route({
    method: 'GET', path: '/',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('index', { css: data });
        });
    }
});

// characters
server.route({
    method: 'GET', path: '/characters',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('characters', { css: data });
        });
    }
});

// freecompany
server.route({
    method: 'GET', path: '/freecompany',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('freecompany', { css: data });
        });
    }
});

// linkshells
server.route({
    method: 'GET', path: '/linkshells',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('linkshells', { css: data });
        });
    }
});

// database
server.route({
    method: 'GET', path: '/database',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('database', { css: data });
        });
    }
});

// lodestone
server.route({
    method: 'GET', path: '/lodestone',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('lodestone', { css: data });
        });
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Database
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// item search
server.route({
    method: 'GET', path: '/database/item/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            name = functions.replaceAll(name, ' ', '+');

        api.setLanguage(request.query.language);
        api.searchItem(reply, {
            name: name
        });
    }
});

// item get
server.route({
    method: 'GET', path: '/database/item/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getItem(reply, {
            id: request.params.id,
        });
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Character
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// character search
server.route({
    method: 'GET', path: '/characters/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.searchCharacter(reply, {
            name: name, server: server
        });
    }
});

// character get
server.route({
    method: 'GET', path: '/characters/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getCharacter(reply, {
            id: request.params.id,
            ignore: request.query.ignore,
            restrict: request.query.restrict,
        });
    }
});

// achievement summary
server.route({
    method: 'GET', path: '/characters/get/{id}/achievements',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getAchievementSummary(reply, {
            id: request.params.id,
        });
    }
});

// achievement all
server.route({
    method: 'GET', path: '/characters/get/{id}/achievements/all',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getAchievementsAll(reply, {
            id: request.params.id,
        });
    }
});

// achievement get kind
server.route({
    method: 'GET', path: '/characters/get/{id}/achievements/{kind}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getAchievements(reply, {
            id: request.params.id,
            kind: request.params.kind
        });
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Freecompany
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// freecompany search
server.route({
    method: 'GET', path: '/freecompany/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.searchFreecompany(reply, {
            name: name,
            server: server,
        });
    }
});

// freecompany get
server.route({
    method: 'GET', path: '/freecompany/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getFreecompany(reply, {
            id: request.params.id
        });

    }
});

// freecompany get
server.route({
    method: 'GET', path: '/freecompany/get/{id}/members',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getFreecompanyMembers(reply, {
            id: request.params.id
        });

    }
});


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Linkshell
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// linkshell search
server.route({
    method: 'GET', path: '/linkshells/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.searchLinkshell(reply, {
            name: name,
            server: server,
        });

    }
});

// linkshell get
server.route({
    method: 'GET', path: '/linkshells/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLinkshell(reply, {
            id: request.params.id,
        });
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Lodestone
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

server.route({
    method: 'GET', path: '/lodestone/banners',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneSlidingBanners(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/topics',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneTopics(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/notices',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneNotices(reply);
    }
});

server.route({
    method: 'GET',path: '/lodestone/maintenance',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneMaintenance(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/updates',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneUpdates(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/status',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneStatus(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/community',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneCommunity(reply);
    }
});

server.route({
    method: 'GET', path: '/lodestone/events',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneEvents(reply);
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Forums
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

server.route({
    method: 'GET', path: '/forums/devtracker',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getDevTracker(reply);
    }
});

server.route({
    method: 'GET', path: '/forums/popularposts',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getPopularPosts(reply);
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Start
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

server.start(function () {
    console.log('Server running at:', server.info.uri);
});
