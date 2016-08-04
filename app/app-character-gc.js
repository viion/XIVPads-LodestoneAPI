var moment = require('moment'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass'),
    XIVDBApi = require('libs/XIVDBClass');

//
// Hold character data
//
class AppCharacterGrandCompanyClass
{
    constructor()
    {
        this.View = require('app/app-character-view');
    }

    //
    // Get grand companies for a specific character
    //
    get(id, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('characters_grandcompany')
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [id], callback);
        return this;
    }

    //
    // Initialize tracking
    //
    init()
    {
        if (!config.settings.autoUpdateCharacters.enableGrandCompanyTracking) {
            return;
        }

        //
        // TODO: I need to make an ID system for grand company ranks,
        // there are 6 tables (3 companies with male and female) all
        // sharing the same ID, bit difficult to handle.
        //
        // XIVDBApi.get('grand_company', (type, grand_company) => {});
        //

        this.trackGrandCompanies();
    }

    //
    // Track minions
    //
    trackGrandCompanies()
    {
        if (!this.View.newData.grand_company.name) {
            return;
        }

        var insertColumns = ['lodestone_id', 'name', 'rank', 'icon'],
            insertData = [
                this.View.lodestoneId,
                this.View.newData.grand_company.name,
                this.View.newData.grand_company.rank,
                this.View.newData.grand_company.icon,
            ];

        // insert character
        database.QueryBuilder
            .insert('characters_grandcompany')
            .insertColumns(insertColumns)
            .insertData([insertData])
            .duplicate(['rank', 'icon']);

        // run query
        database.sql(database.QueryBuilder.get());
    }
}

// Export it
module.exports = new AppCharacterGrandCompanyClass;
