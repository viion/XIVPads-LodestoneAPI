var moment = require('moment'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass');

//
// Hold character data
//
class AppCharacterTrackingClass
{
    constructor()
    {
        this.View = require('app/app-character-view');
    }

    //
    // Get a character via a specific id
    //
    get(id, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('events_tracking')
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [id], callback);
        return this;
    }

    //
    // Initialize tracking
    //
    init()
    {
        for(const [i, field] of ['name', 'server', 'title', 'race', 'clan', 'gender', 'nameday'].entries()) {
            this.track(field);
        }

        // Custom tracking
        this.track('city', 'city', 'name');
        this.track('grand_company_name', 'grand_company', 'name');
        this.track('grand_company_rank', 'grand_company', 'rank');
        this.track('free_company', 'free_company', 'id');
    }

    //
    // Track some field on the characters data, if depth
    // and field passed, this is a more specific second-layer
    // track, if the values change, an event is created.
    //
    track(type, depth, field)
    {
        if (!config.settings.autoUpdateCharacters.enableProfileTracking) {
            return false;
        }

        // get old and new values
        var oldValue = (depth && field) ? this.View.oldData[depth][field] : this.View.oldData[type],
            newValue = (depth && field) ? this.View.newData[depth][field] : this.View.newData[type];

        if (!oldValue || !newValue) {
            return false;
        }

        // ensure length otherwise could be an issue with lodestone
        if (oldValue.length < 2 || newValue.length < 2) {
            return log.echo('--- The length of old or new data was 1 or less for: {type:yellow}', {
                yellow: type,
            });
        }

        // check if the old data is not the same as the new data
        if (oldValue != newValue) {
            // set vars
            var insertColumns = ['time', 'lodestone_id', 'type', 'old_value', 'new_value'],
                insertData = [moment().format('YYYY-MM-DD HH:mm:ss'), '?', '?', '?', '?'],
                binds = [this.View.lodestoneId, type, oldValue, newValue];

            // insert character
            database.QueryBuilder
                .insert('events_tracking')
                .insertColumns(insertColumns)
                .insertData([insertData])
                .duplicate(['lodestone_id']);

            // run query
            database.sql(database.QueryBuilder.get(), binds, () => {
                log.echo('--- [{type:blue}] event added: {old:green} > {new:green}', {
                    type: type,
                    old: oldValue,
                    new: newValue,
                });
            });
        }
    }
}

// Export it
module.exports = new AppCharacterTrackingClass;
