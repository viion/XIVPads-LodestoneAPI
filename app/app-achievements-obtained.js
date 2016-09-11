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
                        this.View.character.lodestone_id,
                        this.View.character.server,
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
                .insertData([[ this.View.character.lodestone_id, JSON.stringify(insertPossible) ]])
                .duplicate(['possible']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('Saved all possible achievements');
            });
        }

        // if we have obtained achievements, lets insert them!
        if (insertData.length > 0) {
            database.QueryBuilder
                .insert('characters_achievements_list')
                .insertColumns(['lodestone_id', 'server', 'achievement_id', 'points', 'obtained'])
                .insertData(insertData)
                .duplicate(['lodestone_id', 'server', 'points', 'obtained']);

            // run query
            database.sql(database.QueryBuilder.get(), [], () => {
                log.echo('Saved {total:blue} achievements', {
                    total: insertData.length
                });
            });

            // update totals
            database.QueryBuilder
                .update('characters')
                .set({
                    achievements_score_reborn_total: insertTotal['reborn'],
                    achievements_score_legacy_total: insertTotal['legacy'],
                })
                .where('lodestone_id = ?');

            // run query
            database.sql(database.QueryBuilder.get(), [ this.View.character.lodestone_id ], () => {
                log.echo('Updated character achievements totals');
            });

            if (insertTotal['reborn'] > this.View.character.achievements_score_reborn) {
                database.QueryBuilder
                    .update('characters')
                    .set({ achievements_last_changed: moment().format('YYYY-MM-DD HH:mm:ss') })
                    .where('lodestone_id = ?');

                database.sql(database.QueryBuilder.get(), [ this.View.character.lodestone_id ]);
                log.echo('Achievements last changed time updated.');
            }
        }
    }
}

// Export it
module.exports = new AppAchievementsTallyClass;
