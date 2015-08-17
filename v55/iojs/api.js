// Setup
var $ = null,
    cheerio = require('cheerio'),
    http = require('http');
    hapi = require('hapi'),
    server = new hapi.Server();

// Server connections
server.connection({ port: 3000 });

// - - - - - - - - - - - - - - - - - - - - - - - - -
// Routes
// - - - - - - - - - - - - - - - - - - - - - - - - -

server.route(
{
    method: 'GET',
    path: '/{id}',

    handler: function (request, reply)
    {
        var id = parseInt(request.params.id),
            html = '',
            options = {
                host: 'eu.finalfantasyxiv.com',
                path: '/lodestone/character/730968',
                port: 80,
            };

        console.log('Getting: ', options.path);
        var start = +new Date();

        // request
        http.get(options, function(res)
        {
            res.on('data', function(data)
            {
                html += data;
            })
            .on('end', function()
            {
                // append html to cheerio
                $ = cheerio.load(html);

                // get title
                var title = $(html).find('title').text();
                console.log('Complete, parsing: ', title);

                var name = $('#contents #character .area_body h2 a').text();
                console.log('name: ', name);

                var server = $('#contents #character .area_body h2 span').text();
                console.log('server: ', server);

                var title = $('#contents #character .area_body h2 .chara_title').text();
                console.log('title: ', title);


                var duration = +new Date() - start;
                console.log('Duration: ', duration);

                reply({
                    name: name,
                    server: server,
                    duration: duration,
                });
            });
        });
    }
});

server.start(function () {
    console.log('Server running at:', server.info.uri);
});