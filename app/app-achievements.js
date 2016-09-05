var config = require('config'),
    async = require('async'),

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
            .from('characters_achievements_list')
            .where('lodestone_id = ?');

        database.sql(database.QueryBuilder.get(), [id], callback);
        return this;
    }

    //
    // Get the census of an achievement
    // - Provides how many people have achievement: {id}
    // - Provides a number of how many people have public achievements
    // - Provides a list of obtained dates
    //
    getCensus(id, callback)
    {
        async.parallel({
            // get list of obtained
            getList: function(asyncCallback) {
                database.QueryBuilder
                    .select()
                    .columns(['lodestone_id', 'server', 'obtained'])
                    .from('characters_achievements_list')
                    .where('achievement_id = ?')
                    .order('obtained', 'asc');

                database.sql(database.QueryBuilder.get(), [id], data => {
                    data = data.length > 0 ? data.rows : null;

                    if (data) {
                        var first = data[0];

                        database.QueryBuilder
                            .select()
                            .columns(['last_updated', 'name', 'server', 'avatar', 'portrait','achievements_score_legacy','achievements_score_reborn'])
                            .from('characters')
                            .where('lodestone_id = ?')
                            .limit(0,1);

                        database.sql(database.QueryBuilder.get(), [first.lodestone_id], character => {
                            asyncCallback(null, {
                                list: data,
                                first: character.rows[0]
                            });
                        });
                    } else {
                        asyncCallback(null, {
                            list: data,
                            first: null
                        });
                    }
                });
            },

            // get how many are eligable
            getEligable: function(asyncCallback) {
                database.QueryBuilder
                    .count()
                    .from('characters')
                    .where('achievements_public = 1');

                database.sql(database.QueryBuilder.get(), [], data => {
                    asyncCallback(null, data.length > 0 ? data.rows : null);
                });
            }
        },
        // finish
        (error, data) => {
            callback({
                list: data.getList.list,
                first: data.getList.first,
                obtained: data.getList.list.length,
                eligable: data.getEligable[0].total,
            });
        });
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
