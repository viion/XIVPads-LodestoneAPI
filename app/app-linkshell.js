var config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    database = require('libs/DatabaseClass');

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
        database.QueryBuilder
            .insert('pending_linkshells')
            .insertColumns(['ls_id'])
            .insertData(idList)
            .duplicate(['ls_id']);

        // run query
        database.sql(database.QueryBuilder.get());
    }
}

// Export it
module.exports = new AppLinkshellClass();
