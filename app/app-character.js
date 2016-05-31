var log = require('../log'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass');

//
// App Character Class
//
class AppCharacterClass
{
    //
    // Add a character to the pending table
    //
    addCharacterToPending(idList) {
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
    }
}

// Export it
module.exports = new AppCharacterClass();
