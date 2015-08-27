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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
        api.getAchievements(id, kind);
    }
});

// character search
server.route(
{
    method: 'GET',
    path: '/',
    handler: function (request, reply) {
        fs.readFile('css/styles.css', 'utf8', function (err,data) {
            reply.view('index', {
                css: data
            });
        });
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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
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

        api.reply = reply;
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
        api.reply = reply;
        api.getLodestoneSlidingBanners();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/topics',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneTopics();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/notices',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneNotices();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/maintenance',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneMaintenance();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/updates',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneUpdates();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/status',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneStatus();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/community',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneCommunity();
    }
});

server.route(
{
    method: 'GET',
    path: '/lodestone/events',

    handler: function (request, reply)
    {
        api.reply = reply;
        api.getLodestoneEvents();
    }
});


// start
server.start(function () {
    console.log('Server running at:', server.info.uri);
});