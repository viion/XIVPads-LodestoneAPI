var moment = require('moment'),
    log = require('../libs/LoggingObject'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass'),
    xivdb = require('../libs/XIVDBClass'),
    api = require('../api/api'),
    events = require('./app-character-events');

//
// App Character Class
//
class AppCharacterClass
{
    //
    // Add a character to the pending table
    //
    addToPending(idList) {
        if (!config.persistent || !idList) {
            return;
        }

        // create query
        querybuilder
            .insert('pending_characters')
            .insertColumns(['lodestone_id'])
            .insertData(idList)
            .duplicate(['lodestone_id']);

        // run query
        database.sql(querybuilder.get());
        return this;
    }

    //
    // Add character to the database
    //
    addCharacter(data, callback)
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
        querybuilder
            .insert('characters')
            .insertColumns(insertColumns)
            .insertData([insertData])
            .duplicate(['lodestone_id']);

        // run query
        database.sql(querybuilder.get(), binds, () => {
            // update characters pending table date
            querybuilder
                .update('pending_characters')
                .set({ 'processed': moment().format('YYYY-MM-DD HH:mm:ss') })
                .where('lodestone_id = ?');

            // run query
            database.sql(querybuilder.get(), [ data.id ], callback);
        });


        return this;
    }

    //
    // Get the last pending characters
    //
    getLastPending(callback)
    {
        querybuilder
            .select()
            .columns('*')
            .from('pending_characters')
            .where(['lodestone_id != 0', 'processed IS NULL'])
            .order('added', 'asc')
            .limit(0,config.settings.autoAddCharacters.limitPerCycle);

        database.sql(querybuilder.get(), [], callback);
        return this;
    }

    //
    // Get the last updated characters
    //
    getLastUpdated(start, callback)
    {
        querybuilder
            .select()
            .columns('*')
            .from('characters')
            .order('last_updated', 'asc')
            .limit(start, config.settings.autoAddCharacters.limitPerCycle);

        database.sql(querybuilder.get(), [], callback);
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

        api.getCharacter(null, { id: lodestoneId }, callback);

        return this;
    }

    //
    // Compare class jobs
    //
    compareClassJobs(oldData, newData)
    {
        // need exp table and classjobs table
        xivdb.getExpTable((expTable) => {
            xivdb.getClasJobs((classjobs) => {
                // setup events and initialize
                events
                    .reset()
                    .setData('oldData', oldData)
                    .setData('newData', newData)
                    .setData('expTable', expTable)
                    .setData('classjobs', classjobs)
                    .init();
            });
        });
    }

    //
    // Track some kind of information
    //
    trackData(type, oldData, newData, isfull)
    {
        if (!isfull) {
            oldData = oldData[type];
            newData = newData[type];
        }

        log.echo('Tracking check: {type:yellow} --> {old:cyan} == {new:cyan}', {
            type: type,
            old: oldData,
            new: newData,
        });

        // check if the old data is not the same as the new data
        if (oldData != newData) {
            var insertColumns = ['time', 'lodestone_id', 'type', 'old_value', 'new_value'],
                insertData = [moment().format('YYYY-MM-DD HH:mm:ss'), '?', '?', '?', '?'],
                binds = [newData.id, type, oldData, newData];

            // insert character
            querybuilder
                .insert('events_tracking')
                .insertColumns(insertColumns)
                .insertData([insertData])
                .duplicate(['lodestone_id']);

            // run query
            database.sql(querybuilder.get(), binds, () => {
                log.echo('Added {type:yellow} tracking event', {
                    type: type,
                });
            });
        }
    }
}

// Export it
module.exports = new AppCharacterClass();
