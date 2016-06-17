var fs = require('fs'),
    os  = require('os-utils');
    moment = require('moment'),
    functions = require('libs/functions.js');

// First, checks if it isn't implemented yet.
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match;
        });
    };
}

//
// Analytics class, to track and debug the xivsync environment
//
class Analytics
{
    constructor()
    {
        this.prefix = null;
        this.counts = {};
    }

    //
    // Record an application event
    //
    record(type, text)
    {
        // ID so lines can be linked
        var id = functions.randomNumber(111111, 888888);

        // this can take a second (cpu cycle poll) so we will do all events in here.
        os.cpuUsage((percent) => {

            // write event
            this.write(id, 'event_' + type, '{0} {1}    Event: {2}'.format(
                this.getTimestamp(),
                functions.padding(Array(15).join(' '), type, true),
                text
            ));

            // write memory
            this.write(id, 'memory', '{0} {1}    Memory: {2}      ({3} / {4})'.format(
                this.getTimestamp(),
                functions.padding(Array(15).join(' '), type, true),
                functions.padding(Array(15).join(' '), this.getMemory(), true),
                os.freemem().toFixed(2),
                os.totalmem().toFixed(2)
            ));

            // write cpu usage
            this.write(id, 'cpu', '{0} {1}    CPU Percent: {2}'.format(
                this.getTimestamp(),
                functions.padding(Array(15).join(' '), type, true),
                percent.toFixed(3)
            ));
        });
    }

    //
    // Add a counter
    //
    count(type, text)
    {
        if (typeof this.counts[type] === 'undefined') {
            this.counts[type] = 0;
        }

        this.counts[type]++;

        // write event
        this.write(0, 'count_' + type, '{0} [{1}] Count: {2} - {3}'.format(
            this.getTimestamp(),
            type,
            this.counts[type],
            text
        ));
    }

    //
    // Write to the log
    //
    write(id, filename, text)
    {
        text = "[{0}][{1}] -- {2}\n".format(this.prefix, id, text);
        fs.appendFile('analytics/'+ global.SPAWN + filename + '.txt', text);
    }


    //
    // Reset (wipes files)
    //
    reset(prefix)
    {
        // set prefix
        this.prefix = prefix;

        // empty files
        fs.readdir('analytics/', (err, files) => {
            if (err) return;

            files.forEach((filename) => {
                fs.writeFile('analytics/' + filename, '');
            });
        });

        return this;
    }

    //
    // Get memory usage
    //
    getMemory()
    {
        return functions.memoryToHuman(functions.memory());
    }

    //
    // Get a timestamp
    //
    getTimestamp()
    {
        return moment().format('HH:mm:ss:SSSS');
    }
}

// Export it
module.exports = new Analytics();
