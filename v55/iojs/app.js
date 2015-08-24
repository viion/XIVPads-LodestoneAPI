// Setup
var api = require('./api/api'),
    hapi = require('hapi'),
    server = new hapi.Server();

// Server connections
server.connection({
    // host: 'xivsync.com',
    port: 3000
});

// - - - - - - - - - - - - - - - - - - - - - - - - -
// Routes
//
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