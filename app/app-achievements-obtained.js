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
class AppAchievementsTallyClass
{
    constructor()
    {
        this.View = require('app/app-achievements-view');
    }

    //
    // Initialize tracking
    //
    init()
    {
        if (!config.settings.autoUpdateAchievements.enableTracking) {
            return;
        }

        // Tally achievement points
        this.track();
    }

    //
    // Track achievements
    //
    track()
    {
        var insertData = [];
        for(var kind in this.View.data) {
            var list = this.View.data[kind];

            // go through entries
            for (const [i, achievement] of list.entries()) {
                if (achievement.obtained) {
                    insertData.push([
                        this.View.lodestoneId,
                        achievement.id,
                        achievement.points,
                        moment.unix(achievement.timestamp).format('YYYY-MM-DD HH:mm:ss')
                    ]);
                }
            }
        }

        // if we have obtained achievements, lets insert them!
        if (insertData.length > 1) {
            database.QueryBuilder
                .insert('characters_achievements')
                .insertColumns(['lodestone_id', 'achievement_id', 'points', 'obtained'])
                .insertData(insertData)
                .duplicate(['lodestone_id']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('--- Saved {total:blue} achievements', {
                    total: insertData.length
                });
            });
        }
    }
}

// Export it
module.exports = new AppAchievementsTallyClass;
