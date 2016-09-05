var async = require('async'),
    sha1 = require('sha1'),
    cron = require('cron').CronJob,
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
            log.echo('Starting Task: Auto-Add Character - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
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
                            // parse the character on lodestone
                            app.Character.getFromLodestone(row.lodestone_id, (data) => {
                                // if character not found, set deleted and move on.
                                if (!data) {
                                    return app.Character.setDeleted(row.lodestone_id);
                                }

                                // process a character add
                                this.addCharacter(data);
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

    //
    // Process a character add
    //
    addCharacter(newData)
    {
        // set data on character class
        var modules = ['Pets', 'Role'];
        for (const [i, module] of modules.entries()) {
            log.echo('Set view: {view:cyan}', { view: module });
            app.Character[module].View.setData(null, newData);
        }

        // run actions
        async.parallel({
            //
            // Manage character roles
            // - classjobs
            // - active class.
            //
            roles: function(callback) {
                // Save gear
                log.echo('[ACTION] CLASSJOBS');
                app.Character.Role.init(() => {
                    callback(null, {
                        classjobs: app.Character.Role.handleClassJobs(),
                        active_class: app.Character.Role.handleActiveClassJob(),
                    });
                });
            },

            //
            // Convert minion names into ids
            //
            pets: function(callback) {
                // Handle pets
                log.echo('[ACTION] PETS');
                app.Character.Pets.init((minions, mounts) => {
                    log.echo('[ACTION] PETS - COMPLETE');
                    callback(null, {
                        minions: minions,
                        mounts: mounts,
                    });
                });
            },
        },
        (error, data) => {
            // override character data
            newData.minions = data.pets.minions;
            newData.mounts = data.pets.mounts;
            newData.classjobs = data.roles.classjobs;
            newData.active_class = data.roles.active_class;

            // get fc id for later
            var freeCompanyId = newData.free_company ? newData.free_company.id : false;

            // remove unnecessary data
            delete newData.active_gear;
            delete newData.active_class.icon;
            delete newData.stats;
            delete newData.free_company;

            // handle free company
            this.handleFreeCompany(freeCompanyId);

            // add the character to the site
            app.Character.addCharacter(newData, () => {
                // set initial hash
                this.setDataHash(newData, () => {
                    log.echo('{note:green}', { note: '✔ Character added successfully.' });
                });
            });
        });
    }

    //
    // Set the characters data hash
    //
    setDataHash(newData, callback)
    {
        var newDataHash = sha1(JSON.stringify(newData));

        database.QueryBuilder
            .update('characters')
            .set({
                data_hash: newDataHash,
                data_last_changed: moment().format('YYYY-MM-DD HH:mm:ss')
            })
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [ newData.id ], callback);
    }

    //
    // Handle Free Company, adds it to the pending list.
    //
    handleFreeCompany(freeCompanyId)
    {
        if (!config.settings.autoUpdateCharacters.enablePlayerFCPending) {
            return;
        }

        // Add free company to pending list
        if (freeCompanyId) {
            app.FreeCompany.addToPending([[ freeCompanyId ]]);
            log.echo('-- Adding players free company ({id:yellow}) to the pending list', {
                id: freeCompanyId,
            });
        }
    }
}

// Export it
module.exports = new autoAddCharactersClass();
