//
// These are paths which are
// not API endpoints.
//
var webpages = [
    '/',
    '/characters',
    '/freecompany',
    '/linkshells',
    '/database',
    '/lodestone',
    '/dev',
    '/web/dev-html.html',
    '/web/bg.jpg',
    '/test'
];

//
// Setup API
//
var fs = require('fs'),
    api = require('./api/api'),
    config = require('./config'),
    functions = require('./functions'),
    hapi = require('hapi'),
    path = require('path');

// create server
var server = new hapi.Server();

// connection options
var options = {
    host: config.host,
    port: config.port,
    routes: {
        files: {
            relativeTo: path.join(__dirname, 'web')
        }
    }
};

// if SSL connection
if (typeof config.hapi.tls !== 'undefined') {
    var options = {
        host: config.host,
        port: config.portssl,
        tls: {
            key: fs.readFileSync(config.hapi.tls.key),
            cert: fs.readFileSync(config.hapi.tls.cert),
        },
        routes: {
            files: {
                relativeTo: path.join(__dirname, 'web')
            }
        }
    };
}

// create server connection
server.connection(options);

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

// register inert
server.register(require('inert'), () => {});

// set headers before response
server.ext('onPreResponse', function(request, reply) {
    var path = request.path;

    if (typeof request.response.header === "function") {
        // check path to dermine if we need json response
        if (webpages.indexOf(path) == -1 || path.indexOf('.jpg') > -1) {
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
//      / - docs
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
//      /database/item/search?name={name}&page={page}
//      /database/item/get/{id}
//      - eg: /database/item/get/fa42e65fb9a
//
//      /database/duty/search?name={name}&page={page}
//      /database/duty/get/{id}
//      -eg: /database/duty/get/02bebe2d92c
//
// - - - - - - - - - - - - - - - - - - - - - - - - -

// Any path in /web
server.route({
    method: 'GET',
    path: '/web/{param*}',
    handler: {
        directory: {
            path: '.',
            redirectToSlash: true,
            index: true
        }
    }
});

// home
server.route({
    method: 'GET', path: '/',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('index', { css: data });
        });
    }
});

// dev
server.route({
    method: 'GET', path: '/dev',
    handler: function (request, reply) {
        fs.readFile('web/dev.js', 'utf8', function (err,data) {
            reply.view('dev', { js: data });
        });
    }
});

// test
server.route({
    method: 'GET', path: '/test',
    handler: function (request, reply) {
        reply.view('test');
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
            name = functions.replaceAll(name, ' ', '+'),
            page = request.query.page ? request.query.page : '';

        api.setLanguage(request.query.language);
        api.searchItem(reply, {
            name: name,
            page: page
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

// recipe search
server.route({
    method: 'GET', path: '/database/recipe/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            name = functions.replaceAll(name, ' ', '+'),
            page = request.query.page ? request.query.page : '';

        api.setLanguage(request.query.language);
        api.searchRecipe(reply, {
            name: name,
            page: page
        });
    }
});

// recipe get
server.route({
    method: 'GET', path: '/database/recipe/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getRecipe(reply, {
            id: request.params.id,
        });
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// duty search
server.route({
    method: 'GET', path: '/database/duty/search',
    handler: function (request, reply) {
        var name = request.query.name ? request.query.name : '',
            name = functions.replaceAll(name, ' ', '+'),
            page = request.query.page ? request.query.page : '';

        api.setLanguage(request.query.language);
        api.searchDuty(reply, {
            name: name,
            page: page
        });
    }
});

// duty get
server.route({
    method: 'GET', path: '/database/duty/get/{id}',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getDuty(reply, {
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
            server = request.query.server ? functions.ucwords(request.query.server) : '',
            page = request.query.page ? request.query.page : 1;

        api.setLanguage(request.query.language);
        api.searchCharacter(reply, {
            name: name, server: server, page: page
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

// get banners
server.route({
    method: 'GET', path: '/lodestone/banners',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneSlidingBanners(reply);
    }
});

// get lodestone topics
server.route({
    method: 'GET', path: '/lodestone/topics',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneTopics(reply);
    }
});

// get notices
server.route({
    method: 'GET', path: '/lodestone/notices',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneNotices(reply);
    }
});

// get maintenance
server.route({
    method: 'GET',path: '/lodestone/maintenance',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneMaintenance(reply);
    }
});

// get updates
server.route({
    method: 'GET', path: '/lodestone/updates',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneUpdates(reply);
    }
});

// get statuses
server.route({
    method: 'GET', path: '/lodestone/status',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneStatus(reply);
    }
});

// get community
server.route({
    method: 'GET', path: '/lodestone/community',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getLodestoneCommunity(reply);
    }
});

// get events
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

// get dev tracker
server.route({
    method: 'GET', path: '/forums/devtracker',
    handler: function (request, reply) {
        api.setLanguage(request.query.language);
        api.getDevTracker(reply);
    }
});

// get popular posts
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
