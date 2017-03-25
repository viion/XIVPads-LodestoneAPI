var https = require('https'),

    // libs
    log = require('libs/LoggingObject'),
    functions = require('libs/functions');

//
// Talk to XIVDB API!
//
class XIVDBClass
{
    constructor()
    {
        this.host = 'api.xivdb.com';

        // api paths
        this.exp_table = '/data/exp_table';
        this.classjobs = '/data/classjobs';
        this.grand_company = '/data/grand_company';
        this.minions = '/minion?columns=id,name_en';
        this.mounts = '/mount?columns=id,name_en';
        this.items = '/item?columns=id,name_en';

        // storage
        this.data = {};
    }

    //
    // Get some data from the XIVDB API
    //
    query(path, onComplete)
    {
        // request options
        var options = {
            host: this.host,
            port: 443,
            path: path,
        }

        log.echo('[XIVDB] Get: {url:cyan}', {
            url: (options.host + options.path),
        });

        // request
        var json = '',
            start = +new Date(),
            memoryStart = functions.memory();


        https.get(options, function(res) {
            res.on('data', function(data) {
                json += data;
            })
            .on('end', function() {
                // end time
                var end = +new Date(),
                    duration = (end - parseInt(start)),
                    memoryFinish = functions.memory();

                log.echo('[XIVDB] >> Complete: {path:yellow} - Duration: {duration:cyan} ms | Memory: {start:cyan} to {finish:cyan}', {
                    path: options.path,
                    duration: duration.toString(),
                    start: functions.memoryToHuman(memoryStart),
                    finish: functions.memoryToHuman(memoryFinish),
                });

                onComplete(JSON.parse(json));
            });
        });
    }

    //
    // Recurrsive get loop
    //
    getRecurrsion(type, callback, loops)
    {
        return callback();
        
        // Prevent crazy recurrsions
        loops = loops ? loops : 1;
        if (loops > 3) {
            return log.echo('[XIVDB] {type:red} has recurrsively called 3 times. Cancelling action, please check Redis is enabled and running.', {
                type: type,
            });
        }
        
         // query xivdb
                return this.query(this[type], (data) => {
                    // set the data and then recurrsively call back
                    storage.set(type, data);
                    this.getRecurrsion(type, callback, loops++);
                });

        // get data for [type] from storage
        storage.get(type, (data) => {
            // if no data, we need to query it
            if (!data) {
                // query xivdb
                return this.query(this[type], (data) => {
                    // set the data and then recurrsively call back
                    storage.set(type, data);
                    this.getRecurrsion(type, callback, loops++);
                });
            }

            // data exists, set it and call onComplete
            this.data[type] = data;
            callback();
        });
    }

    // ---------------------------------------------------------------------------

    //
    // Get some data from the API based on a "type"
    //
    get(type, onComplete)
    {
        this.getRecurrsion(type, () => {
            onComplete(type, this.data[type]);
        });

        return this;
    }
}

// Export it
module.exports = new XIVDBClass();
