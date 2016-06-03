var cron = require('cron').CronJob,
    config = require('../config'),
    log = require('../log'),
    app = require('../app/app');

class autoAddCharactersClass
{
    //
    // Auto Adds Characters, will add them to the
    // database from the "pending" table.
    //
    init()
    {
        if (config.settings.autoAddCharacters.enabled) {
            log.echo('> Start Task: {task:cyan}, Time: {time:cyan}', {
                task: 'Auto Add Characters',
                time: config.settings.autoAddCharacters.cronTime,
            });

            new cron({
                cronTime: config.settings.autoAddCharacters.cronTime,
                onTick: () => {
                    log.echo('Getting {limit:cyan} pending characters ...', {
                        limit: config.settings.autoAddCharacters.limitPerCycle,
                    });

                    // get the last pending character
                    app.Character.getLastPending((data) => {
                        for (const [i, row] of data.rows.entries()) {
                            // parse the character on lodestone
                            app.Character.getFromLodestone(row.lodestone_id, (data) => {
                                // confirmation
                                log.echo('{note:green} :: {id:cyan} - {name:cyan}', {
                                    note: '>> Obtained Lodestone Data',
                                    id: data.id,
                                    name: data.name,
                                });

                                // add the character to the site
                                app.Character.addCharacter(data, (data) => {
                                    log.echo('{note:green}', {
                                        note: '>> Character added successfully.',
                                    });
                                    log.space();
                                });
                            });
                        }
                    });
                },
                start: true,
                timeZone: config.settings.cronTimeZones,
            }).start();
        } else {
            log.echo('> Disabled Task: {task:red}', {
                task: 'Auto Add Characters',
            });
        }
    }
}

// Export it
module.exports = new autoAddCharactersClass();
