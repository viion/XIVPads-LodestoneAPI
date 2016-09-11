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
class AppCharacterGearClass
{
    constructor()
    {
        this.View = require('app/app-character-view');
        this.Role = null;
        this.classjobs = {};
        this.items = {};
        this.callback = null;
    }

    //
    // Initialize tracking
    //
    init(callback)
    {
        if (!config.settings.autoUpdateCharacters.enableGearTracking) {
            return callback ? callback() : false;
        }

        this.callback = callback;

        // We need items
        XIVDBApi.get('items', (type, items) => {
            this.items = items;
            this.trackGear();
        });
    }

    //
    // Track minions
    //
    trackGear(items, classjobs)
    {
        var nameToId = {},
            gearsetData = [];

        // sort mounts into name-to-id
        for (const [i, item] of this.items.entries()) {
            nameToId[item.name_en.toLowerCase()] = item.id;
        }

        // sort gear out into slots and ids
        for(var slot in this.View.newData.active_gear) {
            // get item and item id
            var item = this.View.newData.active_gear[slot],
                itemId = nameToId[item.name.toLowerCase()];

            // push to gearset
            gearsetData.push({
                slot: slot,
                id: itemId ? itemId : null,
                other: itemId ? null : item,
            });
        }

        // I realise I am converting obj to array, this is
        // because I want to visually see the indexes, but
        // the SQL query does not require them.
        var insertData = functions.objToArray({
            lodestone_id: this.View.lodestoneId,
            classjob_id: this.Role.getRoleId(this.View.newData.active_class.name),
            level: this.View.newData.active_class.level,
            gear: '?',
            stats: '?',
        });

        // create binds
        var binds = [
            JSON.stringify(gearsetData),
            JSON.stringify(this.View.newData.stats)
        ];

        // build query
        database.QueryBuilder
            .insert('characters_gearsets')
            .insertColumns(['lodestone_id', 'classjob_id', 'level', 'gear', 'stats'])
            .insertData([insertData])
            .duplicate(['gear', 'stats']);

        // run query
        database.sql(database.QueryBuilder.get(), binds, () => {
            log.echo('Added Gear Set for: {level:blue} {role:blue}', {
                role: this.View.newData.active_class.name,
                level: this.View.newData.active_class.level,
            });

            // finished
            this.callback();
        });
    }
}

// Export it
module.exports = new AppCharacterGearClass;
