var cron = require('cron').CronJob,
    moment = require('moment'),
    config = require('../config'),
    log = require('../libs/LoggingObject'),
    app = require('../app/app');

class autoUpdateAchievements
{
    //
    // Auto-Update Achievements
    // - Calculates the Tally of reborn/legacy (used for ranking)
    // - Inserts all currently obtained achievements as individual rows into the database.
    //
    init(range)
    {
        if (config.settings.autoUpdateAchievements.enabled) {
            var start = range * config.settings.autoUpdateAchievements.limitPerCycle;
            log.echo('Starting Task: Auto-Update Achievements - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
                time: config.settings.autoUpdateAchievements.cronTime,
                limit: config.settings.autoUpdateAchievements.limitPerCycle,
                start: start,
            });

            // start cronjob
            new cron({
                cronTime: config.settings.autoUpdateAchievements.cronTime,
                onTick: () => {
                    log.echo('- Auto-Add {limit:cyan} characters.', {
                        limit: config.settings.autoUpdateAchievements.limitPerCycle,
                    });

                    // get the last pending character
                    app.Achievements.getLastUpdated(start, (data) => {
                        if (data.rows.length == 0) {
                            return log.echo('-- {error:red}', { error: 'No entries.' });
                        }

                        for (const [i, row] of data.rows.entries()) {
                            // parse the character on lodestone
                            app.Achievements.getFromLodestone(row.lodestone_id, (data) => {
                                // if achievements not public, set status and continue
                                if (!data) {
                                    return app.Achievements.setPublicStatus(row.lodestone_id, 0);
                                }

                                // set views
                                app.Achievements.Tally.View.setData(row.lodestone_id, data, row.server);
                                app.Achievements.Obtained.View.setData(row.lodestone_id, data, row.server);

                                // Set achievements public!
                                app.Achievements.setPublicStatus(row.lodestone_id, 1);

                                // Calculate up the total
                                app.Achievements.Tally.init();

                                // Get all the obtained achievements
                                app.Achievements.Obtained.init();
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
module.exports = new autoUpdateAchievements();
