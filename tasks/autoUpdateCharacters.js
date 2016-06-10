var cron = require('cron').CronJob,
    moment = require('moment'),
    config = require('../config'),
    log = require('../libs/LoggingObject'),
    app = require('../app/app');

class autoUpdateCharactersClass
{
    //
    // Auto Updates Characters
    // - Compares EXP to generate EXP events
    // - Compared Levels to generate Level events
    //
    init(range)
    {
        if (config.settings.autoUpdateCharacters.enabled) {
            var start = range * config.settings.autoUpdateCharacters.limitPerCycle;
            log.echo('> Start Task: {task:cyan} - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
                task: 'Auto Update Characters',
                time: config.settings.autoUpdateCharacters.cronTime,
                start: start,
                limit: config.settings.autoUpdateCharacters.limitPerCycle,
            });

            // start cronjob
            new cron({
                cronTime: config.settings.autoUpdateCharacters.cronTime,
                onTick: () => {
                    log.echo('Getting {limit:cyan} characters to process updates...', {
                        limit: config.settings.autoUpdateCharacters.limitPerCycle,
                    });

                    // get the last updated characters
                    app.Character.getLastUpdated(start, (data) => {
                        for (const [i, character] of data.rows.entries()) {
                            log.echo('Updating: {id:cyan} - {name:cyan} ({server:cyan}) - Last Updated: {time:yellow}', {
                                id: character.lodestone_id,
                                name: character.name,
                                server: character.server,
                                time: character.last_updated,
                            });

                            // parse the character on lodestone
                            app.Character.getFromLodestone(character.lodestone_id, (newData) => {
                                // confirmation
                                log.echo('{note:green}: {id:cyan} - {name:cyan}', {
                                    note: '>> Obtained Lodestone Data',
                                    id: newData.id,
                                    name: newData.name,
                                });

                                // get old data
                                var oldData = JSON.parse(character.data);

                                // Compare levels and exp
                                app.Character.compareClassJobs(oldData, newData);

                                // Track stuff
                                app.Character.trackData('name', oldData, newData);
                                app.Character.trackData('server', oldData, newData);
                                app.Character.trackData('title', oldData, newData);
                                app.Character.trackData('race', oldData, newData);
                                app.Character.trackData('clan', oldData, newData);
                                app.Character.trackData('gender', oldData, newData);
                                app.Character.trackData('nameday', oldData, newData);
                                app.Character.trackData('city', oldData.city.name, newData.city.name, true);
                                app.Character.trackData('grand_company_name', oldData.grand_company.name, newData.grand_company.name, true);
                                app.Character.trackData('grand_company_rank', oldData.grand_company.rank, newData.grand_company.rank, true);
                                app.Character.trackData('free_company', oldData.free_company.id, newData.free_company.id, true);

                            });
                        }
                    });
                },
                start: true,
                timeZone: config.settings.cronTimeZones,
            }).start();
        } else {
            log.echo('> Disabled Task: {task:red}', {
                task: 'Auto Update Characters',
            });
        }
    }
}

// Export it
module.exports = new autoUpdateCharactersClass();
