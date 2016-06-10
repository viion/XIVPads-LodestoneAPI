var log = require('../libs/LoggingObject'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass');

//
// App Linkshell Class
//
class AppLinkshellClass
{
    //
    // Add a linkshell to the pending table
    //
    addToPending(idList) {
        if (!config.persistent || !idList) {
            return;
        }

        // create query
        querybuilder
            .insert('pending_linkshells')
            .insertColumns(['ls_id'])
            .insertData(idList)
            .duplicate(['ls_id']);

        // run query
        database.sql(querybuilder.get());
    }
}

// Export it
module.exports = new AppLinkshellClass();
