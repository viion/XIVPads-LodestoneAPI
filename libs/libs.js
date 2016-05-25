var MySQLQueryBuilder = require('./MySQLQueryBuilderClass');

//
// Create libs object
//
var libs = {
    querybuilder: new MySQLQueryBuilder(),
}

// Export it
module.exports = libs;
