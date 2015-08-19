var cheerio = require('cheerio'),
    http = require('http'),
    apiItems = require('./api-items');

// - - - - - - - - - - - - - - - - - - - -
// Lodestone API
// - - - - - - - - - - - - - - - - - - - -

var api =
{
    reply: null,

    memory: function()
    {
        return process.memoryUsage().heapUsed;
    },

    /**
     * Get html from a web page
     *
     * @param url - url for options for http.get
     * @param callback - function to callback on
     */
    get: function(url, callback)
    {
        console.log('Get Path:', url);

        options.host = 'eu.finalfantasyxiv.com';
        options.port = 80;

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
        api.get(apiItems.getUrl('search', name), function($) {
            api.reply(apiItems.getSearchResults($));
        });
    }

    getItem: function(id)
    {
        console.log('Getting item for ID:', id);
        api.get(apiItems.getUrl('item', id), function($) {
            api.reply(apiItems.getItemData($));
        });
    }
}

// Export it
module.exports = api;