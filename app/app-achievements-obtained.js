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
        var insertPossible = [],
            insertData = [],
            insertTotal = {
                reborn: 0,
                legacy: 0,
            };

        for(var kind in this.View.data) {
            var list = this.View.data[kind];

            // go through entries
            for (const [i, achievement] of list.entries()) {
                // Add to possible
                insertPossible.push(achievement.id);

                // if obtained, add to insert data
                if (achievement.obtained) {
                    insertData.push([
                        this.View.lodestoneId,
                        achievement.id,
                        achievement.points,
                        moment.unix(achievement.timestamp).format('YYYY-MM-DD HH:mm:ss')
                    ]);
                }

                // totals
                var key = (achievement.kind == 13) ? 'legacy' : 'reborn';
                insertTotal[key] = insertTotal[key] + achievement.points;
            }
        }

        // if we have a possible amount
        if (insertPossible.length > 0) {
            database.QueryBuilder
                .insert('characters_achievements_possible')
                .insertColumns(['lodestone_id', 'possible'])
                .insertData([[ this.View.lodestoneId, JSON.stringify(insertPossible) ]])
                .duplicate(['possible']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('--- Saved all possible achievements');
            });
        }

        // if we have obtained achievements, lets insert them!
        if (insertData.length > 0) {
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

        // update players totals
        if (insertData.length > 0) {
            database.QueryBuilder
                .update('characters')
                .set({
                    achievements_score_reborn_total: insertTotal['reborn'],
                    achievements_score_legacy_total: insertTotal['legacy'],
                })
                .where('lodestone_id = ?');

            // run query
            database.sql(database.QueryBuilder.get(), [this.View.lodestoneId], () => {
                log.echo('--- Updated character achievements');
            });
        }
    }
}

// Export it
module.exports = new AppAchievementsTallyClass;
