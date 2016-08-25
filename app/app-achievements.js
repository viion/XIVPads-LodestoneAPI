var config = require('config'),

    // libs
    log = require('libs/LoggingObject'),
    database = require('libs/DatabaseClass'),

    // sync api
    SyncApi = require('api/api');

//
// App Achievements Class
//
class AppAchievementsClass
{
    constructor()
    {
        this.Tally = require('app/app-achievements-tally');
        this.Obtained = require('app/app-achievements-obtained');
    }

    //
    // Get achievements for a specific character
    //
    get(id, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('characters_achievements')
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [id], callback);
        return this;
    }

    //
    // Get all possible achievement ids for a specific character
    //
    getPossible(id, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('characters_achievements_possible')
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [id], callback);
        return this;
    }

    //
    // Get the last updated characters
    //
    getLastUpdated(start, callback)
    {
        database.QueryBuilder
            .select()
            .columns('*')
            .from('characters')
            .order('achievements_last_updated', 'asc')
            .limit(start, config.settings.autoUpdateAchievements.limitPerCycle);

        database.sql(database.QueryBuilder.get(), [], callback);
        return this;
    }

    //
    // Get achievements from lodestone
    //
    getFromLodestone(lodestoneId, callback)
    {
        log.echo('Requesting {id:cyan} from lodestone', {
            id: lodestoneId,
        });

        SyncApi.getAchievementsAll(null, { id: lodestoneId }, callback);
        return this;
    }

    //
    // Set character as not having achievements public
    //
    setPublicStatus(lodestoneId, status)
    {
        database.QueryBuilder
            .update('characters')
            .set({ achievements_public: status })
            .where(['lodestone_id = ?']);

        database.sql(database.QueryBuilder.get(), [ lodestoneId ]);
    }
}

// Export it
module.exports = new AppAchievementsClass();
