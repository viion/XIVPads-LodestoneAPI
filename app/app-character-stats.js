var moment = require('moment'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass');

//
// Manages the attribute statistics for characters.
//
class AppCharacterStatsClass
{
    constructor()
    {
        this.View = require('app/app-character-view');

        this.attributeToValue = {};
        this.insertData = [];
    }

    //
    // Character attributes are compared with what is currently
    // stored in the database, if this characters attributes
    // are higher in any field, those are u pdated with this
    // characters values, lodestone_id, and time.
    //
    // A full database record is not stored, so it is not
    // possible with this setup to know who is "second", only
    // who is the highest.
    //
    // This relies entirely what is shown on lodestone, if someone
    // uses a potion to get higher but it not shown on lodestone,
    // then tough luck, if they did use a potion and it shows
    // on lodestone, then great! It is impossible to deduct.
    //
    init()
    {
        if (!config.settings.autoUpdateCharacters.enableAttributeStatsRanking) {
            return;
        }
        
        // get current stats
        database.QueryBuilder
            .select()
            .columns('*')
            .from('ranking_stats');

        //
        database.sql(database.QueryBuilder.get(), [], (data) => {
            // loop through attributes to organize by name and value
            for (const [i, attr] of data.rows.entries()) {
                this.attributeToValue[attr.name] = attr.value;
            }

            this.check('core', this.View.newData.stats.core);
            this.check('attributes', this.View.newData.stats.attributes);
            this.check('elemental', this.View.newData.stats.elemental);
            this.check('properties', this.View.newData.stats.properties);
            this.check('resistances', this.View.newData.stats.resistances);

            // if insert data
            if (this.insertData.length > 0) {
                database.QueryBuilder
                    .insert('ranking_stats')
                    .insertColumns(['lodestone_id', 'name', 'value', 'type'])
                    .insertData(this.insertData)
                    .duplicate(['lodestone_id', 'value']);

                database.sql(database.QueryBuilder.get());
            }
        });
    }

    //
    // Check a group of stats to see if they're higher
    //
    check(type, attributes)
    {
        // compare attribute values
        for(var id in attributes) {
            var attr = attributes[id];

            // if value is higher than on record
            if (typeof this.attributeToValue[id] === 'undefined' || attr.value > this.attributeToValue[id]) {
                this.insertData.push([this.View.lodestoneId, id, attr.value, type]);
            }
        }
    }
}

// Export it
module.exports = new AppCharacterStatsClass;
