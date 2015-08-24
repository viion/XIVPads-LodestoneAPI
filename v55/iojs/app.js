// Setup
var fs = require('fs'),
    api = require('./api/api'),
    config = require('./config'),
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

// character search
server.route(
{
    method: 'GET',
    path: '/',
    handler: function (request, reply) {
        fs.readFile('css/styles.css', 'utf8', function (err,data) {
            console.log(data);
            reply.view('index', {
                css: data
            });
        });
    }
});

// character search
server.route(
{
    method: 'GET',
    path: '/character/search/{name}/{server?}',

    handler: function (request, reply)
    {
        var name = request.params.name,
            server = request.params.server ? request.params.server : '';

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

// item search
server.route(
{
    method: 'GET',
    path: '/item/search/{name}',

    handler: function (request, reply)
    {
        var name = request.params.name;

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

server.start(function () {
    console.log('Server running at:', server.info.uri);
});