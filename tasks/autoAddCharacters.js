var cron = require('cron').CronJob,
    moment = require('moment'),
    config = require('../config'),
    log = require('../libs/LoggingObject'),
    app = require('../app/app');

class autoAddCharactersClass
{
    //
    // Auto Adds Characters, will add them to the
    // database from the "pending" table.
    //
    init(range)
    {
        if (config.settings.autoAddCharacters.enabled) {
            var start = range * config.settings.autoAddCharacters.limitPerCycle;
            log.echo('Starting Task: Auto-Update Achievements - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
                time: config.settings.autoAddCharacters.cronTime,
                limit: config.settings.autoAddCharacters.limitPerCycle,
                start: start,
            });

            // start cronjob
            new cron({
                cronTime: config.settings.autoAddCharacters.cronTime,
                onTick: () => {
                    log.echo('- Auto-Add {limit:cyan} characters.', {
                        limit: config.settings.autoAddCharacters.limitPerCycle,
                    });

                    // get the last pending character
                    app.Character.getLastPending(start, (data) => {
                        if (data.rows.length == 0) {
                            return log.echo('-- {error:red}', { error: 'No entries.' });
                        }

                        for (const [i, row] of data.rows.entries()) {
                            global.ANALYTICS.record('cronjob', 'Add ID: '+ row.lodestone_id);

                            // parse the character on lodestone
                            app.Character.getFromLodestone(row.lodestone_id, (data) => {
                                // if achievements not public, set status and continue
                                if (!data) {
                                    return app.Character.setDeleted(row.lodestone_id);
                                }

                                // add the character to the site
                                app.Character.addCharacter(data, (data) => {
                                    global.ANALYTICS.record('cronjob', 'Add ID Completed: '+ row.lodestone_id);
                                    log.echo('-- {note:green}', { note: 'Character added successfully.' });
                                    log.space();
                                });
                            });
                        }
                    });
                },
                start: config.settings.cronStart,
                runOnInit: config.settings.cronRunOnInit,
                timeZone: config.settings.cronTimeZones,
            }).start();
        } else {
            log.echo('{range:yellow} {task:red}', {
                range: range,
                task: 'Auto-Add Task Disabled',
            });
        }
    }
}

// Export it
module.exports = new autoAddCharactersClass();
