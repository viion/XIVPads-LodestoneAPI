var redis = require('redis'),
    log = require('./LoggingObject'),
    config = require('../config'),
    zlib = require('zlib');

//
// Handles the setup of xivsync
//
class StorageClass
{
    constructor()
    {
        // if persistent disabled, don't do anything
        if (!config.persistent) {
            return;
        }

        this.client = redis.createClient();
        this.client.on("error", function (err) {
            log.echo('[REDIS] Error: {error:red}', {
                error: err,
            });
        });
    }

    //
    // Set some data
    //
    set(key, value)
    {
        log.echo('[REDIS] {method:green}: {key:cyan}', {
            method: 'SET',
            key: key,
        });

        value = JSON.stringify(value);
        this.client.set(key, value);
    }

    //
    // Get some data
    //
    get(key, callback) {
        log.echo('[REDIS] {method:green}: {key:cyan}', {
            method: 'GET',
            key: key,
        });

        this.client.get(key, (err, data) => {
            if (err) {
                return log.echo('[REDIS] Error: {error:red}', {
                    error: err,
                });
            }

            callback(JSON.parse(data));
        });
    }

    //
    // Compress some data
    //
    compress(data, callback)
    {
        if (config.zlibStorage) {
            zlib.deflate(data, (error, buffer) => {
                if (!error) {
                    return callback(buffer.toString('base64'));
                } else {
                    console.error('Error compressing:');
                    console.error(error);
                    return callback(data);
                }
            });
        } else {
            return callback(data);
        }
    }

    //
    // Decompress some data
    //
    decompress(data, callback)
    {
        if (config.zlibStorage) {
            var data = Buffer.from(data, 'base64');
            zlib.inflate(data, (error, buffer) => {
                if (!error) {
                    return callback(buffer.toString());
                } else {
                    console.error('Error decompressing:');
                    console.error(error);
                    return callback(data);
                }
            });
        } else {
            return callback(data);
        }
    }

}

// Export it
module.exports = new StorageClass();
