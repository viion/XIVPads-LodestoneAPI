var moment = require('moment'),
    log = require('../log'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass'),
    api = require('../api/api');

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
        var insertColumns = ['lodestone_id', 'last_updated', 'name', 'server', 'avatar', 'portrait', 'data'],
            insertData = [data.id, moment().format('YYYY-MM-DD HH:mm:ss'), '?', '?', '?', '?', '?'];

        // bind data
        var binds = [
            data.name,
            data.server,
            data.avatar,
            data.portrait,
            JSON.stringify(data),
        ];

        // create query
        querybuilder
            .insert('characters')
            .insertColumns(insertColumns)
            .insertData([insertData])
            .duplicate(['lodestone_id']);

        // run query
        database.sql(querybuilder.get(), binds, callback);
        return this;
    }

    //
    // Get the last pending character
    //
    getLastPending(callback)
    {
        querybuilder
            .select()
            .columns('*')
            .from('pending_characters')
            .where(['lodestone_id != 0', 'processed IS NULL'])
            .order('added', 'asc')
            .limit(0,config.settings.autoAddLimit);

        database.sql(querybuilder.get(), [], callback);
        return this;
    }

    //
    // Get a character from lodestone
    //
    getFromLodestone(lodestoneId, callback) {
        log.echo('Requesting {id:cyan} from lodestone', {
            id: lodestoneId,
        });

        api.getCharacter(null, { id: lodestoneId }, callback);

        return this;
    }
}

// Export it
module.exports = new AppCharacterClass();
