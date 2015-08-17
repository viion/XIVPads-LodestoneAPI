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
        console.log(process.memoryUsage().heapUsed);

        // get name
        var name = request.params.name;
        if (!name) {
            reply('No name passed.'); return;
        }

        api.reply = reply;
        api.searchItem(name);

        console.log(process.memoryUsage().heapUsed);
    }
});

server.start(function () {
    console.log('Server running at:', server.info.uri);
});