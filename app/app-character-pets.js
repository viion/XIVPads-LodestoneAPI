var moment = require('moment'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass'),
    XIVDBApi = require('libs/XIVDBClass');

//
// Hold character data
//
class AppCharacterPetsClass
{
    constructor()
    {
        this.View = require('app/app-character-view');
    }

    //
    // Initialize tracking
    //
    init(callback)
    {
        if (!config.settings.autoUpdateCharacters.enablePetsTracking) {
            return callback ? callback() : false;
        }

        // need minions and mounts from xivdb!
        XIVDBApi.get('minions', (type, minions) => {
            XIVDBApi.get('mounts', (type, mounts) => {
                callback(
                    this.convertMinions(minions),
                    this.convertMounts(mounts)
                );
            });
        });
    }

    //
    // Convert minion data into known ids
    //
    convertMinions(minions)
    {
        var nameToId = {},
            data = [];

        // sort minions into name-to-id
        for (const [i, minion] of minions.entries()) {
            nameToId[minion.name_en.toLowerCase()] = minion.id;
        }

        // get real ids
        for (const [i, minion] of this.View.newData.minions.entries()) {
            var minionId = nameToId[minion.name.toLowerCase()];

            // if minion id, store that, otherwise store lodestone response.
            minionId
                ? data.push([ minionId, null ])
                : data.push([ null, minion ]);
        }

        return data;
    }

    //
    // convert mount data into known ids
    //
    convertMounts(mounts)
    {
        var nameToId = {},
            data = [];

        // sort mounts into name-to-id
        for (const [i, mount] of mounts.entries()) {
            nameToId[mount.name_en.toLowerCase()] = mount.id;
        }

        //  get real ids
        for (const [i, mount] of this.View.newData.mounts.entries()) {
            var mountId = nameToId[mount.name.toLowerCase()];

            // if mount id, store that, otherwise store lodestone response.
            mountId
                ? data.push([ mountId, null ])
                : data.push([ null, mount ]);
        }

        return data;
    }
}

// Export it
module.exports = new AppCharacterPetsClass;
