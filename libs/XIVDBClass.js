var https = require('https'),
    storage = require('../libs/StorageClass.js'),
    functions = require('../Libs/functions'),
    log = require('./LoggingObject');

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

        log.echo('Get: {url:cyan}', {
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

                log.echo('>> Complete: {path:yellow} - Duration: {duration:cyan} ms | Memory: {start:cyan} to {finish:cyan}', {
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
    getRecurrsion(table, callback, loops)
    {
        // Prevent crazy recurrsions
        loops = loops ? loops : 1;
        if (loops > 3) {
            return log.echo('{table:red} has recurrsively called 3 times. Cancelling action, please check Redis is enabled and running.', {
                table: table,
            });
        }

        // get data for [table] from storage
        storage.get(table, (data) => {
            // if no data, we need to query it
            if (!data) {
                // query xivdb
                return this.get(this[table], (data) => {
                    // set the data and then recurrsively call back
                    storage.set(table, data);
                    this.getRecurrsion(table, callback, loops++);
                });
            }

            // data exists, set it and call onComplete
            this.data[table] = data;
            callback();
        });
    }

    // ---------------------------------------------------------------------------

    //
    // Get the EXP table
    //
    getExpTable(onComplete, loops)
    {
        this.getRecurrsion('exp_table', () => {
            onComplete(this.data['exp_table']);
        });
    }

    //
    // Get the ClassJobs data
    //
    getClasJobs(onComplete, loops)
    {
        this.getRecurrsion('classjobs', () => {
            onComplete(this.data['classjobs']);
        });
    }
}

// Export it
module.exports = new XIVDBClass();
