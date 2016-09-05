var async = require('async'),
    sha1 = require('sha1'),
    cron = require('cron').CronJob,
    storage = require('libs/StorageClass'),
    moment = require('moment'),
    config = require('config'),
    log = require('libs/LoggingObject'),
    app = require('app/app');

class autoUpdateCharactersClass
{
    //
    // Auto Updates Characters
    // - Compares EXP to generate EXP events
    // - Compares Levels to generate Level events
    // - Tracks Attributes against a stored list to get the highest values
    // - Tracks profile information for changes (name, server, race, free company)
    // - Tracks minions and mounts
    // - Tracks individual Grand Companies
    // - Tracks gearsets (with their stats and active class)
    // - Adds free company to the site (if it doesn't exist)
    //
    init(range)
    {
        if (config.settings.autoUpdateCharacters.enabled) {
            var start = range * config.settings.autoUpdateCharacters.limitPerCycle;
            log.echo('Starting Task: Auto-Update Characters - Time: {time:cyan} - Range: {start:yellow} ({limit:yellow})', {
                time: config.settings.autoUpdateCharacters.cronTime,
                limit: config.settings.autoUpdateCharacters.limitPerCycle,
                start: start,
            });

            // start cronjob
            new cron({
                cronTime: config.settings.autoUpdateCharacters.cronTime,
                onTick: () => {
                    log.echo('- Auto-Update {limit:cyan} characters.', {
                        limit: config.settings.autoUpdateCharacters.limitPerCycle,
                    });

                    // get the last updated characters
                    app.Character.getLastUpdated(start, (data) => {
                        if (data.rows.length == 0) {
                            return log.echo('-- {error:red}', { error: 'No entries.' });
                        }

                        // loop through selected characters
                        for (const [i, character] of data.rows.entries()) {
                            log.echo('-- {id:cyan} - {name:cyan} ({server:cyan}) - Last Updated: {time}', {
                                id: character.lodestone_id,
                                name: character.name,
                                server: character.server,
                                time: character.last_updated,
                            });

                            // get character data
                            this.getData(character, (newData, oldData) => {
                                // update the character
                                this.runUpdate(character, newData, oldData, (response) => {
                                    log.echo('-- {id:green} {note:green}', {
                                        id: character.lodestone_id,
                                        note: 'Character updated successfully.'
                                    });
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
                task: 'Auto-Update Task Disabled',
            });
        }
    }

    //
    // Get the newest character data from lodestone
    // and get the old data from storage
    //
    getData(character, callback2)
    {
        async.parallel({
            //
            // Get the new character data from lodestone
            //
            getNewData: callback => {
                app.Character.getFromLodestone(character.lodestone_id, (newData) => {
                    callback(null, newData)
                });
            },

            //
            // Get the old character data from storage
            //
            getOldData: callback => {
                storage.decompress(character.data, (oldData) => {
                    callback(null, oldData);
                });
            },
        },
        (error, data) => {
            callback2(data.getNewData, data.getOldData);
        });
    }

    //
    // Run a character update
    //
    runUpdate(character, newData, oldData, callback3)
    {
        if (!newData) {
            console.error('No new data obtained ....');
            return;
        }

        // parse json
        oldData = oldData ? JSON.parse(oldData) : false;

        // set data on character class
        var modules = ['Events', 'Tracking', 'Pets', 'GrandCompany', 'Gear', 'Role'];
        for (const [i, module] of modules.entries()) {
            log.echo('Set view: {view:cyan}', { view: module });
            app.Character[module].View.setData(oldData, newData);
        }

        // initialize rules
        app.Character.Role.init(() => {

            // run actions
            async.parallel({
                //
                // Generate EXP/Level events
                // - Requires: oldData
                //
                events:  function(callback) {
                    if (!oldData) {
                        return callback(null, []);
                    }

                    // track events
                    log.echo('[ACTION] {action:blue}', { action: 'EXP/LV EVENTS' });
                    app.Character.Events.Role = app.Character.Role;
                    app.Character.Events.init(() => {
                        log.echo('[ACTION] COMPLETE {action:blue}', { action: 'EXP/LV EVENTS' });
                        return callback(null, []);
                    });
                },

                //
                // Generate tracking data
                // - Requires: oldData
                //
                tracking: function(callback) {
                    if (!oldData) {
                        return callback(null, []);
                    }

                    // Track profile information
                    log.echo('[ACTION] {action:blue}', { action: 'PROFILE' });
                    app.Character.Tracking.init(() => {
                        log.echo('[ACTION] COMPLETE {action:blue}', { action: 'PROFILE' });
                        return callback(null, []);
                    });
                },

                //
                // Record their current grand company
                //
                grandcompany: function(callback) {
                    if (!newData.grand_company) {
                        return callback(null, []);
                    }

                    // save grand company
                    log.echo('[ACTION] {action:blue}', { action: 'GRAND COMPANIES' });
                    app.Character.GrandCompany.init(() => {
                        log.echo('[ACTION] COMPLETE {action:blue}', { action: 'GRAND COMPANIES' });
                        return callback(null, []);
                    });
                },

                //
                // Record their current gearsets
                //
                gearsets: function(callback) {
                    // Save gear
                    log.echo('[ACTION] {action:blue}', { action: 'GEARSETS' });
                    app.Character.Gear.Role = app.Character.Role;
                    app.Character.Gear.init(() => {
                        log.echo('[ACTION] COMPLETE {action:blue}', { action: 'GEARSETS' });
                        return callback(null, []);
                    });
                },

                //
                // Convert minion names into ids
                //
                pets: function(callback) {
                    // Handle pets
                    log.echo('[ACTION] {action:blue}', { action: 'PETS' });
                    app.Character.Pets.init((minions, mounts) => {
                        log.echo('[ACTION] COMPLETE {action:blue}', { action: 'CLASSJOBS' });
                        return callback(null, {
                            minions: minions,
                            mounts: mounts,
                        });
                    });
                },

                //
                // Manage character roles
                // - classjobs
                // - active class.
                //
                roles: function(callback) {
                    // Save gear
                    log.echo('Reorganize roles ...');
                    return callback(null, {
                        classjobs: app.Character.Role.handleClassJobs(),
                        active_class: app.Character.Role.handleActiveClassJob(),
                    });
                },
            },
            // finish
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
                delete newData.free_company.name;
                delete newData.free_company.icon;

                // handle free company
                this.handleFreeCompany(freeCompanyId);

                // check hash
                this.checkDataHash(character, newData, oldData);

                // Update character
                app.Character.updateCharacter(newData, () => {
                    callback3();
                });
            });

        });
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

    //
    // Check the characters data hash
    //
    checkDataHash(character, newData, oldData)
    {
        // generate hash for data
        var newDataHash = sha1(JSON.stringify(newData));
        if (newDataHash != character.data_hash) {
            log.echo('[NEW HASH] {newHash:yellow} != {oldHash:red}', {
                newHash: newDataHash,
                oldHash: character.data_hash,
            });

            database.QueryBuilder
                .update('characters')
                .set({
                    data_hash: newDataHash,
                    data_last_changed: moment().format('YYYY-MM-DD HH:mm:ss')
                })
                .where('lodestone_id = ?');

            database.sql(database.QueryBuilder.get(), [ character.lodestone_id ]);
        } else {
            log.echo('[HASH] No change: {newHash:yellow} == {oldHash:red}', {
                newHash: newDataHash,
                oldHash: character.data_hash,
            });
        }
    }
}

// Export it
module.exports = new autoUpdateCharactersClass();
