var mysql = require('mysql'),
    config = require('config'),
    sha1 = require('sha1'),

    // libs
    functions = require('libs/functions.js'),
    storage = require('libs/StorageClass'),
    log = require('libs/LoggingObject');

//
// Simple mysql query builder
// - XIVSync
//
class DatabaseClass
{
    constructor()
    {
        this.QueryBuilder = require('libs/QueryBuilderClass');

        // if persistent disabled, don't do anything
        if (!config.persistent) {
            return;
        }

        // Setup Server
        this.connection = mysql.createPool(
        {
            host:       config.db.host,
            user:       config.db.user,
            password:   config.db.pass,
            database:   config.db.table,
            debug:      config.db.debug,
            socketPath: config.db.socket,
        });

        this.cache = true;
    }

    noCache()
    {
        this.cache = false;
        return this;
    }

    //
    // run an sql query
    //
    sql(sql, binds, callback)
    {
        var command = sql.split(' ')[0],
            key = sha1(sql + (binds ? binds.join('') : ''));

        // if SQL command is select, check storage
        if (this.cache && command == 'SELECT') {
            storage.get(key, (data) => {
                if (data) {
                    return callback(data);
                }

                this.execute(sql, binds, callback, key);
            });
        } else {
            this.execute(sql, binds, callback);
        }
    }

    //
    // execute an SQL statement (called based
    // on the callback conditions from the function: sql)
    //
    execute(sql, binds, callback, key)
    {
        var randomId = functions.randomNumber(0, 99999);
        global.ANALYTICS.record('database', 'Starting SQL Query: '+ randomId + ' --- '+ sql);
        global.ANALYTICS.count('database', sql);

        // if persistent disabled, don't do anything
        if (!config.persistent) {
            return;
        }

        // Get the connection
        this.connection.getConnection(function(error, connection)
        {
            // If any errors, throw the exception
            if (error) {
                throw error;
            }

            log.echo("[DB][{id:red}] SQL: {sql:purple}", {
                id: randomId,
                sql: config.settings.sqlStatementTruncate ? sql.substring(0, config.settings.sqlStatementTruncate) + '...' : sql
            });

            // Run the query
            connection.query(sql, binds, function(error, rows, fields)
            {
                global.ANALYTICS.record('database', 'Completed SQL Query: '+ randomId);

                // If any errors, throw the exception
                if (error)
                {
                    log.echo("[DB][{id:red}] {arrow:green} {error:red}", {
                        id: randomId,
                        arrow: '>>',
                        error: error
                    });

                    // Return, if specific function exists, call that,
                    // otherwise its an inline function and does not require
                    // the client to be sent back
                    if (typeof callback !== 'undefined')
                    {
                        callback(error);
                    }
                }
                else
                {
                    // Disconnect this query
                    connection.release();
                    log.echo("[DB][{id:red}] {arrow:green} Database query complete", {
                        id: randomId,
                        arrow: '>>'
                    });

                    // Setup a return object
                    var obj = {
                        time: new Date(),
                        length: rows.length,
                        rows: rows,
                    }

                    // if storage key
                    if (this.cache && key) {
                        storage.set(key, obj, config.persistentTimeout);
                    }

                    // Return, if specific function exists, call that,
                    // otherwise its an inline function and does not require
                    // the client to be sent back
                    if (typeof callback !== 'undefined')
                    {
                        callback(obj);
                    }

                    // reset cache status
                    this.cache = true;
                }
            });
        });
    }
}

// Export it
module.exports = new DatabaseClass();
