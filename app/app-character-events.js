var moment = require('moment'),
    config = require('config'),

    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass'),
    XIVDBApi = require('libs/XIVDBClass');

//
// Create EXP/Level events for characters based on progression
// between old and new character data.
//
class AppCharacterEventsClass
{
    constructor()
    {
        this.View = require('app/app-character-view');
        this.Role = null;

        this.exp_table = {};
        this.eventsLevels = [];
        this.eventsExp = [];

        this.callback = null;
    }

    //
    // Reset
    //
    reset()
    {
        this.exp_table = {};
        this.eventsLevels = [];
        this.eventsExp = [];
        return this;
    }

    //
    // Setup class
    //
    init(callback)
    {
        if (!config.settings.autoUpdateCharacters.enableProgressEvents) {
            return callback ? callback() : false;
        }

        this.callback = callback;

        // need exp table and classjobs table
        XIVDBApi.get('exp_table', (type, exp_table) => {
            // setup events and check
            this.reset();
            this.exp_table = exp_table;
            this.check();
        });
    }

    //
    // Events are progress gains by the character, such as
    // gaining experience points or levels. The old data is
    // compared against the new data and from that we can
    // figure out how much EXP or levels have been gained.
    //
    // This is bundled up into an "event" and stored into
    // the database for retrival when the character data
    // is requested.
    //
    check()
    {
        // Some some vars
        var timeNow = moment().format('YYYY-MM-DD HH:mm:ss'),
            maxLevel = this.exp_table[this.exp_table.length - 1].level;

        // new and old data
        var newClassJobData = this.View.newData.classjobs,
            oldClassJobData = this.View.oldData.classjobs;

        var eventsLevels = [],
            eventsExp = [];

        // loop through classes
        for(var classname in this.View.newData.classjobs) {
            var newRole = newClassJobData[classname],
                roleId = this.Role.getRoleId(newRole.name),
                oldRole = oldClassJobData[roleId],
                oldTotalExp = this.getTotalExp(oldRole.level, oldRole.exp.current),
                newTotalExp = this.getTotalExp(newRole.level, newRole.exp.current);

            // if id not found
            if (!roleId) {
                log.echo('{error:red}', {
                    error: 'Role ID cannot be found for: '+ newRole.name,
                });
                continue;
            }

            // if the OLD class is max level, we don't need to do anything
            // or if the new level is 0
            //
            // - this is a forced skip as we don't want to generate exp events
            //   even if this section fails.
            if (oldRole.level >= maxLevel || newRole.level == 0) {
                continue;
            }

            // if the OLD level is higher than the NEW level, it broke!
            // (can happen on cache isues, just continue and ignore)
            //
            // - this is a forced skip as we don't want to generate exp events
            //   even if this section fails.
            if (oldRole.level > newRole.level) {
                continue;
            }

            // If the old was 0 and the new was 30, its likely
            // to be one of the new jobs, skip the event as
            // that isn't really "earned"
            // (0 - 1317680 is the exp relative)
            if (oldRole.level == 0 && newRole.level == 30 || oldTotalExp == 0 && newTotalExp == 1317680) {
                continue;
            }

            // if level has increased, create an event!
            if (newRole.level > oldRole.level) {
                var levelsGained = (newRole.level - oldRole.level);

                // Double check it's not below zero
                if (levelsGained > 0) {
                    // I realise I am converting obj to array, this is
                    // because I want to visually see the indexes, but
                    // the SQL query does not require them.
                    var newEvent = functions.objToArray({
                        lodestone_id: this.View.lodestoneId,
                        time: timeNow,
                        jobclass: roleId,
                        gained: levelsGained,
                        old: oldRole.level,
                        new: newRole.level,
                    });

                    // create event
                    this.eventsLevels.push(newEvent);
                    log.echo('--- Levels: {value:blue} {cjname:cyan}', {
                        value: levelsGained,
                        cjname: newRole.name,
                    });
                }
            }

            // if exp has increased, create an event!
            if (newTotalExp > oldTotalExp) {
                var expGained = (newTotalExp - oldTotalExp)

                // Ensure the gained EXP is above the threshold limit
                if (expGained >= config.settings.autoUpdateCharacters.minimumExpForEvent) {
                    // I realise I am converting obj to array, this is
                    // because I want to visually see the indexes, but
                    // the SQL query does not require them.
                    var newEvent = functions.objToArray({
                        lodestone_id: this.View.lodestoneId,
                        time: timeNow,
                        jobclass: roleId,
                        gained: expGained,
                        old: oldTotalExp,
                        new: newTotalExp,
                    });

                    // create event
                    this.eventsExp.push(newEvent);
                    log.echo('--- EXP: {value:blue} {cjname:cyan}', {
                        value: expGained,
                        cjname: newRole.name,
                    });
                }
            }
        }

        // finish
        if (this.eventsLevels.length > 0 || this.eventsExp.length > 0) {
            this.insertNewEvents();
        } else {
            this.callback();
        }
    }

    //
    // Insert new EXP or level events into the database.
    //
    insertNewEvents()
    {
        var insertColumns = ['lodestone_id', 'time', 'jobclass', 'gained', 'old', 'new'];

        // if level events
        if (this.eventsLevels.length > 0) {
            database.QueryBuilder
                .insert('characters_events_lvs_new')
                .insertColumns(insertColumns)
                .insertData(this.eventsLevels)
                .duplicate(['lodestone_id']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('--- Added {total:blue} levelling events', {
                    total: this.eventsLevels.length,
                });

                // if level events
                if (this.eventsExp.length > 0) {
                    database.QueryBuilder
                        .insert('characters_events_exp_new')
                        .insertColumns(insertColumns)
                        .insertData(this.eventsExp)
                        .duplicate(['lodestone_id']);

                    // run query
                    database.sql(database.QueryBuilder.get(), [], () => {
                        log.echo('--- Added {total:blue} experience points events', {
                            total: this.eventsExp.length,
                        });

                        this.callback();
                    });
                } else {
                    this.callback();
                }
            });
        } else {
            this.callback();
        }
    }

    //
    // Get the total exp based on the "level" and the "current exp", this is done
    // by looping through the EXP Table and adding up the total until the level is
    // met, the current EXP is then added on as the remainder.
    //
    getTotalExp(currentLevel, currentExp)
    {
        var totalExpGained = 0;
        for (const [i, row] of this.exp_table.entries()) {
            if (row.level < currentLevel) {
                totalExpGained += row.exp;
            }
        }

        return totalExpGained + currentExp;
    }
}

// Export it
module.exports = new AppCharacterEventsClass();
