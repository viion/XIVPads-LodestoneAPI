var config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    database = require('libs/DatabaseClass');

//
// App FreeCompany Class
//
class AppFreeCompanyClass
{
    //
    // Add a free company to the pending table
    //
    addToPending(idList) {
        if (!config.persistent || !idList) {
            return;
        }

        // create query
        database.QueryBuilder
            .insert('pending_freecompanies')
            .insertColumns(['fc_id'])
            .insertData(idList)
            .duplicate(['fc_id']);

        // run query
        database.sql(database.QueryBuilder.get());
    }
}

// Export it
module.exports = new AppFreeCompanyClass();
