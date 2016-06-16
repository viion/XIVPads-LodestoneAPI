require('app-module-path/register');
global.TIMESTAMP = 0;
global.ANALYTICS = require('libs/Analytics');
global.ANALYTICS.reset();

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

// node modules
var fs = require('fs'),
    hapi = require('hapi'),
    path = require('path'),
    config = require('config'),

    api = require('api/api'),
    app = require('app/app'),

    // libs
    functions = require('libs/functions'),
    log = require('libs/LoggingObject');

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

    if (request.query.pretty) {
        request.response.header('Content-Type', 'text/html');
        var html = JSON.stringify(request.response.source, null, 2),
            html = `<pre>${html}</pre>`;

        reply(html);
        return;
    }


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
//      /characters/search?name={name}&server={server}&page={page}
//      /characters/get/{id}
//      /characters/get/{id}/achievements/
//      /characters/get/{id}/achievements/{kind}
//
//      /freecompany
//      /freecompany/search?name={name}&server={server}&page={page}
//      /freecompany/get/{id}
//      /freecompany/get/{id}/members?page={page}
//
//      /linkshells
//      /linkshells/search?name={name}&server={server}&page={page}
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
            page = request.query.page ? request.query.page : 1;

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
            page = request.query.page ? request.query.page : 1;

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
            page = request.query.page ? request.query.page : 1;

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
    handler: (request, reply) => {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '',
            page = request.query.page ? request.query.page : 1;

        api.setLanguage(request.query.language);
        api.searchCharacter(reply, {
            name: name, server: server, page: page
        }, (data) => {
            // Build ID list
            var idList = [];
            for(var i in data.results) {
                idList.push([data.results[i].id]);
            }

            app.Character.addToPending(idList);
        });
    }
});

// character get
server.route({
    method: 'GET', path: '/characters/get/{id}',
    handler: (request, reply) => {
        api.setLanguage(request.query.language);
        api.getCharacter(reply, {
            id: request.params.id,
            ignore: request.query.ignore,
            restrict: request.query.restrict,
        }, (data) => {
            app.Character.addToPending([[data.id]]);
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
            page = request.query.page ? request.query.page : 1;

        api.setLanguage(request.query.language);
        api.searchFreecompany(reply, {
            name: name,
            server: server,
            page: page
        }, (data) => {
            // Build ID list
            var idList = [];
            for(var i in data.results) {
                idList.push([data.results[i].id]);
            }

            app.FreeCompany.addToPending(idList);
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
        }, (data) => {
            app.FreeCompany.addToPending([[data.id]]);
        });
    }
});

// freecompany get members
server.route({
    method: 'GET', path: '/freecompany/get/{id}/members',
    handler: function (request, reply) {
        var page = request.query.page ? request.query.page : 1;

        api.setLanguage(request.query.language);
        api.getFreecompanyMembers(reply, {
            id: request.params.id,
            page: page
        }, (data) => {
            // build ID list
            var idList = [];
            for(var i in data.results) {
                idList.push([data.results[i].id]);
            }

            app.Character.addToPending(idList);
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
            server = request.query.server ? functions.ucwords(request.query.server) : '',
            page = request.query.page ? request.query.page : 1;

        api.setLanguage(request.query.language);
        api.searchLinkshell(reply, {
            name: name,
            server: server,
            page: page,
        }, (data) => {
            // Build ID list
            var idList = [];
            for(var i in data.results) {
                idList.push([data.results[i].id]);
            }

            app.Linkshell.addToPending(idList);
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
        }, (data) => {
            app.Linkshell.addToPending([[data.id]]);
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
    log.line();
    log.echo('Server running at: {address:purple}', {
        address: server.info.uri,
    });
    log.line();
});
