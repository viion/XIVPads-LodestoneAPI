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
    init()
    {
        if (!config.settings.autoUpdateCharacters.enablePetsTracking) {
            return;
        }

        // need minions and mounts from xivdb!
        XIVDBApi.get('minions', (type, minions) => {
            XIVDBApi.get('mounts', (type, mounts) => {
                this.trackMinions(minions);
                this.trackMounts(mounts);
            });
        });
    }

    //
    // Track minions
    //
    trackMinions(minions)
    {
        var nameToId = {},
            insertData = [];

        // sort minions into name-to-id
        for (const [i, minion] of minions.entries()) {
            nameToId[minion.name_en.toLowerCase()] = minion.id;
        }

        // get insert data
        for (const [i, minion] of this.View.newData.minions.entries()) {
            var minionId = nameToId[minion.name.toLowerCase()];

            // if minion id, store that, otherwise store lodestone response.
            minionId
                ? insertData.push([ this.View.lodestoneId, minionId, null ])
                : insertData.push([ this.View.lodestoneId, null, JSON.stringify(minion) ]);
        }

        // if we have data
        if (insertData && insertData.length > 1) {
            database.QueryBuilder
                .insert('characters_minions')
                .insertColumns(['lodestone_id', 'minion_id', 'other'])
                .insertData(insertData)
                .duplicate(['lodestone_id', 'minion_id', 'other']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('--- Saved {total:blue} minions', {
                    total: insertData.length
                });
            });
        }
    }

    //
    // Track minions
    //
    trackMounts(mounts)
    {
        var nameToId = {},
            insertData = [];

        // sort mounts into name-to-id
        for (const [i, mount] of mounts.entries()) {
            nameToId[mount.name_en.toLowerCase()] = mount.id;
        }

        // get insert data
        for (const [i, mount] of this.View.newData.mounts.entries()) {
            var mountId = nameToId[mount.name.toLowerCase()];

            // if mount id, store that, otherwise store lodestone response.
            mountId
                ? insertData.push([ this.View.lodestoneId, mountId, null ])
                : insertData.push([ this.View.lodestoneId, null, JSON.stringify(mount) ]);
        }

        // if we have data
        if (insertData && insertData.length > 1) {
            database.QueryBuilder
                .insert('characters_mounts')
                .insertColumns(['lodestone_id', 'mount_id', 'other'])
                .insertData(insertData)
                .duplicate(['lodestone_id', 'mount_id', 'other']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('--- Saved {total:blue} mounts', {
                    total: insertData.length
                });
            });
        }
    }
}

// Export it
module.exports = new AppCharacterPetsClass;
