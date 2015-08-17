var cheerio = require('cheerio'),
    http = require('http'),
    apiItems = require('./api-items');

// - - - - - - - - - - - - - - - - - - - -
// Lodestone API
// - - - - - - - - - - - - - - - - - - - -

var api =
{
    reply: null,

    /**
     * Get html from a web page
     *
     * @param options - options for http.get
     * @param callback - function to callback on
     */
    get: function(options, callback)
    {
        console.log('Get Path:', options.path);

        // get
        var html = '',
            start = +new Date();

        console.log('- Start: ', start);

        // request
        http.get(options, function(res)
        {
            res.on('data', function(data)
            {
                html += data;
            })
            .on('end', function()
            {
                // end time
                var end = +new Date(),
                    duration = (end - start);

                console.log('- End: ', end);
                console.log('- Duration: ', duration);

                // callback with a cheerio assigned html
                callback(cheerio.load(html));
            });
        });
    },

    /**
     * Search items by name
     *
     * @param name - name to search for
     */
    searchItem: function(name)
    {
        console.log('Searching for Item:', name);

        // start
        api.get(apiItems.getSearchOptions(name), function($) {
            api.reply(apiItems.getSearchResults($));
        });
    }
}

// Export it
module.exports = api;