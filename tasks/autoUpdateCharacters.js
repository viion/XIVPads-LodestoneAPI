var cron = require('cron').CronJob,
    moment = require('moment'),
    config = require('config'),
    log = require('libs/LoggingObject'),
    app = require('app/app');

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
            log.echo('- Starting Task - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
                time: config.settings.autoUpdateCharacters.cronTime,
                limit: config.settings.autoUpdateCharacters.limitPerCycle,
                start: start,
            });

            // start cronjob
            new cron({
                cronTime: config.settings.autoUpdateCharacters.cronTime,
                onTick: () => {
                    log.echo('-- {limit:cyan} characters.', {
                        limit: config.settings.autoUpdateCharacters.limitPerCycle,
                    });

                    // get the last updated characters
                    app.Character.getLastUpdated(start, (data) => {
                        for (const [i, character] of data.rows.entries()) {
                            log.echo('-- {id:cyan} - {name:cyan} ({server:cyan}) - Last Updated: {time}', {
                                id: character.lodestone_id,
                                name: character.name,
                                server: character.server,
                                time: character.last_updated,
                            });

                            // parse the character on lodestone
                            app.Character.getFromLodestone(character.lodestone_id, (newData) => {
                                // get old data
                                var oldData = JSON.parse(character.data);

                                // set data on character class
                                // TODO: Find a better way to do this, eg class inheritence
                                var modules = ['Events', 'Tracking', 'Stats', 'Pets', 'GrandCompany', 'Gear'];
                                for (const [i, module] of modules.entries()) {
                                    app.Character[module].View.setData(oldData, newData);
                                }

                                // Compare levels and exp

                                log.echo('-- Testing EXP/Level Events');
                                app.Character.Events.init();

                                // Calculate up the attributes
                                log.echo('-- Checking Attribute Statistics');
                                app.Character.Stats.init();

                                // Track stuff
                                log.echo('-- Tracking Profile Information');
                                app.Character.Tracking.init();

                                // Record minions and mounts
                                log.echo('-- Recording Minions/Mounts');
                                app.Character.Pets.init();

                                // Record grand companies
                                log.echo('-- Track Grand Companies');
                                app.Character.GrandCompany.init();

                                // Save gear
                                log.echo('-- Saving Gear Set');
                                app.Character.Gear.init();

                                // Add free company to pending list
                                if (config.settings.autoUpdateCharacters.enablePlayerFCPending && newData.free_company && typeof newData.free_company.id !== 'undefined') {
                                    app.FreeCompany.addToPending([newData.free_company.id]);
                                    log.echo('-- Adding players free company ({id:yellow}) to the pending list', {
                                        id: newData.free_company.id,
                                    });
                                }
                            });
                        }
                    });
                },
                start: true,
                timeZone: config.settings.cronTimeZones,
            }).start();
        } else {
            log.echo('{task:red}', {
                task: 'Auto-Update Task Disabled',
            });
        }
    }
}

// Export it
module.exports = new autoUpdateCharactersClass();
