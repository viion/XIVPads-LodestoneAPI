var moment = require('moment'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    database = require('libs/DatabaseClass'),

    // sync api
    SyncApi = require('api/api');

//
// App Character Class
//
class AppCharacterClass
{
    constructor()
    {
        this.Events = require('app/app-character-events');
        this.Tracking = require('app/app-character-tracking');
        this.Stats = require('app/app-character-stats');
        this.Pets = require('app/app-character-pets');
        this.GrandCompany = require('app/app-character-gc');
        this.Gear = require('app/app-character-gear');
    }

    //
    // Add a character to the pending table
    //
    addToPending(idList)
    {
        if (!config.persistent || !idList) {
            return;
        }

        // create query
        database.QueryBuilder
            .insert('pending_characters')
            .insertColumns(['lodestone_id'])
            .insertData(idList)
            .duplicate(['lodestone_id']);

        // run query
        database.sql(database.QueryBuilder.get());
        return this;
    }

    //
    // Add character to the database
    //
    addCharacter(data, callback, isUpdate)
    {
        var insertColumns = ['last_updated', 'lodestone_id', 'name', 'server', 'avatar', 'portrait', 'data'],
            insertData = [moment().format('YYYY-MM-DD HH:mm:ss'), '?', '?', '?', '?', '?', '?'];

        // bind data
        var binds = [
            data.id,
            data.name,
            data.server,
            data.avatar,
            data.portrait,
            JSON.stringify(data),
        ];

        // insert character
        database.QueryBuilder
            .insert('characters')
            .insertColumns(insertColumns)
            .insertData([insertData])
            .duplicate(['lodestone_id', 'name', 'server', 'avatar', 'portrait', 'data']);

        // run query
        database.sql(database.QueryBuilder.get(), binds, () => {
            // update characters pending table date
            // - this is only done if we're not updating a character
            if (!isUpdate) {
                database.QueryBuilder
                    .update('pending_characters')
                    .set({ processed: moment().format('YYYY-MM-DD HH:mm:ss') })
                    .where('lodestone_id = ?');

                // run query
                database.sql(database.QueryBuilder.get(), [ data.id ], callback);
            }
        });

        return this;
    }

    //
    // Update a character
    // - This is an alias to addCharacter since the
    //   code would be the same.
    //
    updateCharacter(data, callback)
    {
        this.addCharacter(data, callback, true);
    }

    //
    // Get the last pending characters
    //
    getLastPending(callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('pending_characters')
            .where(['lodestone_id != 0', 'processed IS NULL', 'deleted = 0'])
            .order('added', 'asc')
            .limit(0,config.settings.autoAddCharacters.limitPerCycle);

        database.sql(database.QueryBuilder.get(), [], callback);
        return this;
    }

    //
    // Get the last updated characters
    //
    getLastUpdated(start, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('characters')
            .order('last_updated', 'asc')
            .limit(start, config.settings.autoAddCharacters.limitPerCycle);

        database.sql(database.QueryBuilder.get(), [], callback);
        return this;
    }

    //
    // Get a character from lodestone
    //
    getFromLodestone(lodestoneId, callback)
    {
        log.echo('Requesting {id:cyan} from lodestone', {
            id: lodestoneId,
        });

        SyncApi.getCharacter(null, { id: lodestoneId }, callback);
        return this;
    }

    //
    // Set a character as deleted
    //
    setDeleted(lodestoneId)
    {
        log.echo('Marking character {id:red} as deleted.', {
            id: lodestoneId,
        });

        database.QueryBuilder
            .update('pending_characters')
            .set({
                processed: moment().format('YYYY-MM-DD HH:mm:ss'),
                deleted: 1
            })
            .where('lodestone_id = ?');

        // run query
        database.sql(database.QueryBuilder.get(), [ lodestoneId ], (callback));
    }
}

// Export it
module.exports = new AppCharacterClass();
