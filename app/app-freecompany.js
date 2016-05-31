var log = require('../log'),
    config = require('../config'),
    database = require('../libs/DatabaseClass'),
    querybuilder = require('../libs/QueryBuilderClass');

//
// App FreeCompany Class
//
class AppFreeCompanyClass
{
    //
    // Add a free company to the pending table
    //
    addFreeCompanyToPending(idList) {
        if (!config.persistent || !idList) {
            return;
        }

        // create query
        querybuilder
            .insert('pending_freecompanies')
            .insertColumns(['fc_id'])
            .insertData(idList)
            .duplicate(['fc_id']);

        // run query
        database.sql(querybuilder.get());
    }
}

// Export it
module.exports = new AppFreeCompanyClass();
