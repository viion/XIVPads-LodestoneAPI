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
        if (!config.settings.autoUpdateAchievements.enableTallyPoints) {
            return;
        }

        // Tally achievement points
        this.track();
    }

    //
    // Track achievement scores
    //
    track()
    {
        var reborn = 0,
            legacy = 0;

        for(var kind in this.View.data) {
            var list = this.View.data[kind];

            // go through entries
            for (const [i, achievement] of list.entries()) {
                if (achievement.obtained) {
                    // whether to update legacy or reborn count
                    (kind == 13)
                        ? legacy = legacy + achievement.points
                        : reborn = reborn + achievement.points;
                }
            }
        }

        // if points, update their scores
        if (reborn > 0 || legacy > 0) {
            database.QueryBuilder
                .update('characters')
                .set({
                    achievements_last_updated: moment().format('YYYY-MM-DD HH:mm:ss'),
                    achievements_score_reborn: reborn,
                    achievements_score_legacy: legacy,
                })
                .where(['lodestone_id = ?']);

            database.sql(database.QueryBuilder.get(), [ this.View.character.lodestone_id ], () => {
                log.echo('Achievement scores updated ({legacy:blue} Legacy) ({reborn:blue} Reborn)', {
                    legacy: legacy,
                    reborn: reborn,
                });
            });
        }
    }
}

// Export it
module.exports = new AppAchievementsTallyClass;
