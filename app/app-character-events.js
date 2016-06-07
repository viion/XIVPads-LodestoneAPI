var moment = require('moment'),
    log = require('../log'),
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

    setData(key, data)
    {
        this.data[key] = data;
        return this;
    }

    init()
    {
        // Some some vars
        var lodestoneId = this.data.oldData.id,
            timeNow = moment().format('YYYY-MM-DD HH:mm:ss'),
            maxLevel = this.data.expTable[this.data.expTable.length - 1].level,
            maxExp = 0;

        for (const [i, row] of this.data.expTable.entries()) {
            maxExp = maxExp + row.exp;
        }

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
                oldRole = oldClassJobData[classname];

            // if the OLD class is max level, we don't need to do anything
            if (oldRole.level == maxLevel) {
                continue;
            }

            // if the OLD level is higher than the NEW level, it broke!
            // (can happen on cache isues, just continue and ignore)
            if (oldRole.level > newRole.level) {
                continue;
            }

            // if level has increased, create an event
            if (newRole.level > oldRole.level) {
                var levelsGained = (newRole.level - oldRole.level);

                // Double check it's not below zero
                if (levelsGained < 0) {
                    continue;
                }

                // create event
                this.levelEvents.push({
                    lodestone_id: lodestoneId,
                    time: timeNow,
                    jobclass: newRole.name,
                    gained: levelsGained,
                    old: oldRole.level,
                    new: newRole.level,
                });
            }
        }

        console.log(this.levelEvents);
    }
}

// Export it
module.exports = new AppCharacterEventsClass();
