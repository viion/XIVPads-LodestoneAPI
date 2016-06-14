var log = require('libs/LoggingObject'),
    xivdb = require('libs/XIVDBClass');

//
// Handles the setup of xivsync
//
class SetupClass
{
    constructor()
    {
        this.onComplete = null;
        this.tasks = {
            exp_table: false,
            classjobs: false,
            //grand_company: false,
            minions: false,
            mounts: false,
            items: false,
        }
    }

    //
    // Get stuff
    //
    init(onComplete)
    {
        log.echo('{title:blue}', {
            title: 'Running XIVSync Setup',
        });

        // set onComplete function
        this.onComplete = onComplete;

        // Get data from XIVDB Database
        for(var type in this.tasks) {
            xivdb.get(type, (type) => { this.check(type) })
        }
    }

    //
    // Check if completed
    //
    check(setupTaskCompleted)
    {
        // update task list
        this.tasks[setupTaskCompleted] = true;

        // go through tasks and set it to false if any are not finished
        var finished = true;
        for(var id in this.tasks) {
            if(!this.tasks[id]) {
                finished = false;
            }
        }

        // run onComplete callback
        if (finished) {
            log.echo('{title:blue}', {
                title: 'Setup Complete!',
            });

            log.space();
            this.onComplete();
        }
    }
}

// Export it
module.exports = new SetupClass();
