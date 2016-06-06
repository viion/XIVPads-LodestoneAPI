var https = require('https'),
    storage = require('../libs/StorageClass.js')
    log = require('../log');

//
// Simple mysql query builder
// - XIVSync
//
class XIVDBClass
{
    constructor()
    {
        this.host = 'api.xivdb.com';
        this.exp_table = '/data/exp_table';
        this.classjobs = '/data/classjobs';
        this.data = {};
    }

    //
    // Get some data from the XIVDB API
    //
    get(path, onComplete)
    {
        log.echo('XIVDB - Starting a new request ...');

        // request options
        var options = {
            host: this.host,
            port: 443,
            path: path,
        }

        log.echo('Sending request: {url:cyan}', {
            url: (options.host + options.path),
        });

        // request
        var json = '';
        https.get(options, function(res) {
            res.on('data', function(data) {
                json += data;
            })
            .on('end', function() {
                log.echo ('>> Complete');
                onComplete(JSON.parse(json));
            });
        });
    }

    //
    // Get the EXP Table
    //
    getExpTable(onComplete, loops)
    {
        // Prevent crazy recurrsions
        loops = loops ? loops : 1;
        if (loops > 3) {
            return log.echo('getExpTable has recurrsively called 3 times. Cancelling action, please check Redis is enabled and running.');
        }

        // tru get exp table from redis
        storage.get('exp_table', (data) => {
            // if no data, we need to query it
            if (!data) {
                // query xivdb exp table
                return this.get(this.exp_table, (data) => {
                    // set the exp table and then recurrsively call back
                    storage.set('exp_table', data);
                    this.getExpTable(onComplete, loops++);
                });
            }

            // data exists, set it and call onComplete
            this.data.exp_table = data;
            onComplete();
        });
    }
}

// Export it
module.exports = new XIVDBClass();
