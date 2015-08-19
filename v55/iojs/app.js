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
// - - - - - - - - - - - - - - - - - - - - - - - - -

server.route(
{
    method: 'GET',
    path: '/item/search/{name}',

    handler: function (request, reply)
    {
        console.log(api.memory());

        // get name
        var name = request.params.name;
        if (!name) {
            reply('No name passed.'); return;
        }

        api.reply = reply;
        api.searchItem(name);

        console.log(api.memory());
    }
});

server.start(function () {
    console.log('Server running at:', server.info.uri);
});