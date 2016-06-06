var log = require('./log'),
    xivdb = require('./libs/XIVDBClass');

//
// Handles the setup of xivsync
//
class SetupClass
{
    constructor()
    {
        this.onComplete = null;
        this.tasks = {
            getExpTable: false,
        }
    }

    //
    // Get stuff
    //
    init(onComplete) {
        log.echo('{title:blue}', {
            title: 'Running XIVSync Setup',
        });

        // set onComplete function
        this.onComplete = onComplete;

        // ---------------------------------------

        // Get EXP Table
        xivdb.getExpTable(() => {
            log.echo('Obtained EXP Table');
            this.check('getExpTable');
        });
    }

    //
    // Check if completed
    //
    check(setupTaskCompleted) {
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
