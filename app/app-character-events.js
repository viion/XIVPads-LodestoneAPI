var moment = require('moment'),
    log = require('../libs/LoggingObject'),
    functions = require('../libs/functions'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass'),
    xivdb = require('../libs/XIVDBClass'),
    api = require('../api/api');

//
// App Character Events Class
//
class AppCharacterEventsClass
{
    constructor()
    {
        this.data = {};
        this.levelEvents = [];
        this.expEvents = [];
    }

    //
    // Reset
    //
    reset()
    {
        this.data = {};
        this.levelEvents = [];
        this.expEvents = [];
        return this;
    }

    //
    // Set some data for this class to use.
    //
    setData(key, data)
    {
        this.data[key] = data;
        return this;
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
    init()
    {
        // Some some vars
        var lodestoneId = this.data.oldData.id,
            timeNow = moment().format('YYYY-MM-DD HH:mm:ss'),
            maxLevel = this.data.expTable[this.data.expTable.length - 1].level;

        // Start comparison
        log.echo('>> Compare: {compare:cyan}', {
            compare: 'Class/Jobs',
        });

        // new and old data
        var newClassJobData = this.data.newData.classjobs,
            oldClassJobData = this.data.oldData.classjobs;

        var levelEvents = [],
            expEvents = [];

        // loop through classes
        for(var classname in this.data.newData.classjobs) {
            var newRole = newClassJobData[classname],
                oldRole = oldClassJobData[classname],
                oldTotalExp = this.getTotalExp(oldRole.level, oldRole.exp.current),
                newTotalExp = this.getTotalExp(newRole.level, newRole.exp.current),
                jobclassId = this.getRoleId(newRole.name);

            // if id not found
            if (!jobclassId) {
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

            // if level has increased, create an event!
            if (newRole.level > oldRole.level) {
                var levelsGained = (newRole.level - oldRole.level);

                // Double check it's not below zero
                if (levelsGained > 0) {
                    // I realise I am converting obj to array, this is
                    // because I want to visually see the indexes, but
                    // the SQL query does not require them.
                    var newEvent = functions.objToArray({
                        lodestone_id: lodestoneId,
                        time: timeNow,
                        jobclass: jobclassId,
                        gained: levelsGained,
                        old: oldRole.level,
                        new: newRole.level,
                    });

                    // create event
                    this.levelEvents.push(newEvent);
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
                        lodestone_id: lodestoneId,
                        time: timeNow,
                        jobclass: jobclassId,
                        gained: expGained,
                        old: oldTotalExp,
                        new: newTotalExp,
                    });

                    // create event
                    this.expEvents.push(newEvent);
                }
            }
        }

        // finish
        if (this.levelEvents.length > 0 || this.expEvents.length > 0) {
            this.insertNewEvents();
        }
    }

    //
    // Insert new EXP or level events into the database.
    //
    insertNewEvents()
    {
        var insertColumns = ['lodestone_id', 'time', 'jobclass', 'gained', 'old', 'new'];

        // if level events
        if (this.levelEvents.length > 0) {
            querybuilder
                .insert('events_lvs_new')
                .insertColumns(insertColumns)
                .insertData(this.levelEvents)
                .duplicate(['lodestone_id']);

            // run query
            database.sql(querybuilder.get(), [], () => {
                log.echo('Added {total:yellow} levelling events', {
                    total: this.levelEvents.length,
                });
            });
        }

        // if level events
        if (this.expEvents.length > 0) {
            querybuilder
                .insert('events_exp_new')
                .insertColumns(insertColumns)
                .insertData(this.expEvents)
                .duplicate(['lodestone_id']);

            // run query
            database.sql(querybuilder.get(), [], () => {
                log.echo('Added {total:yellow} experience points events', {
                    total: this.expEvents.length,
                });
            });
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
        for (const [i, row] of this.data.expTable.entries()) {
            if (row.level < currentLevel) {
                totalExpGained += row.exp;
            }
        }

        return totalExpGained + currentExp;
    }

    //
    // Get the real ClassJob ID for the role, this is matched by lowercasing the name
    // and matching against the two names, returns false if no match, which will
    // skip any event creation.
    //
    getRoleId(role)
    {
        for (const [i, row] of this.data.classjobs.entries()) {
            if (row.name_en.toLowerCase() == role.toLowerCase()) {
                return row.id;
                break;
            }
        }

        return false;
    }
}

// Export it
module.exports = new AppCharacterEventsClass();
