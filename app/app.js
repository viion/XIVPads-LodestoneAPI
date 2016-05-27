var log = require('../log'),
    queryBuilder = require('../libs/MySQLQueryBuilderClass');

//
// Apps class
//
class AppClass
{
    //
    // Add a character to the pending table
    //
    addCharacterToPending(data) {
        var lodestoneId = data.id;
        log.echo("Adding the Lodestone ID: {lodestoneId:cyan} to the {table:yellow} table", {
            lodestoneId: lodestoneId,
            table: 'characters_pending',
        });
    }
}

// Export it
module.exports = new AppClass();
