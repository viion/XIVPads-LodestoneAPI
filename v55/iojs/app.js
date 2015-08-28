// Setup
var fs = require('fs'),
    api = require('./api/api'),
    config = require('./config'),
    functions = require('./functions'),
    hapi = require('hapi'),
    path = require('path');
    server = new hapi.Server();

// Server connections
server.connection({
    host: config.host,
    port: config.port,
});

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

// - - - - - - - - - - - - - - - - - - - - - - - - -
// Routes
//
//      / - home!
//      /character/search/{name}
//      /character/get/{id}
//      /character/get/{id}/achievements/
//      /character/get/{id}/achievements/{kind}
//
//      /item/search/{name}
//      /item/get/{id}
//
// - - - - - - - - - - - - - - - - - - - - - - - - -

// home
server.route(
{
    method: 'GET',
    path: '/',
    handler: function (request, reply) {
        fs.readFile('views/styles.css', 'utf8', function (err,data) {
            reply.view('index', { css: data });
        });
    }
});


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Database
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// item search
server.route(
{
    method: 'GET',
    path: '/item/search',

    handler: function (request, reply)
    {
        var name = request.query.name ? request.query.name : '';

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.searchItem(name);

    }
});

// item get
server.route(
{
    method: 'GET',
    path: '/item/get/{id}',

    handler: function (request, reply)
    {
        var id = request.params.id;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getItem(id);
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Character
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// character search
server.route(
{
    method: 'GET',
    path: '/character/search',

    handler: function (request, reply)
    {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.searchCharacter(name, server);
    }
});

// character get
server.route(
{
    method: 'GET',
    path: '/character/get/{id}',

    handler: function (request, reply)
    {
        var id = request.params.id;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getCharacter(id, {
            ignore: request.query.ignore,
            restrict: request.query.restrict,
        });
    }
});

// achievement summary
server.route(
{
    method: 'GET',
    path: '/character/get/{id}/achievements',

    handler: function (request, reply)
    {
        var id = request.params.id;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getAchievementSummary(id);
    }
});

// achievement get
server.route(
{
    method: 'GET',
    path: '/character/get/{id}/achievements/{kind}',

    handler: function (request, reply)
    {
        var id = request.params.id,
            kind = request.params.kind;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getAchievements(id, kind);
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Freecompany
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// freecompany search
server.route(
{
    method: 'GET',
    path: '/freecompany/search',

    handler: function (request, reply)
    {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.searchFreecompany(name, server);

    }
});

// freecompany get
server.route(
{
    method: 'GET',
    path: '/freecompany/get/{id}',

    handler: function (request, reply)
    {
        var id = request.params.id;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getFreecompany(id);

    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Linkshell
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// linkshell search
server.route(
{
    method: 'GET',
    path: '/linkshell/search',

    handler: function (request, reply)
    {
        var name = request.query.name ? request.query.name : '',
            server = request.query.server ? functions.ucwords(request.query.server) : '';

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.searchLinkshell(name, server);

    }
});

// linkshell get
server.route(
{
    method: 'GET',
    path: '/linkshell/get/{id}',

    handler: function (request, reply)
    {
        var id = request.params.id;

        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLinkshell(id);
    }
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Lodestone
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

server.route(
{
    method: 'GET',
    path: '/lodestone/banners',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneSlidingBanners();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/topics',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneTopics();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/notices',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneNotices();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/maintenance',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneMaintenance();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/updates',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneUpdates();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/status',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneStatus();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/community',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneCommunity();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/events',

    handler: function (request, reply)
    {
        api.setLanguage(request.query.language);
        api.setReply(reply);
        api.getLodestoneEvents();
    }
});


// start
server.start(function () {
    console.log('Server running at:', server.info.uri);
});