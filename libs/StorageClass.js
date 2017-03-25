var log = require('./LoggingObject'),
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
    }

    //
    // Set some data
    //
    set(key, value, expire)
    {
        return this;
        
        log.echo('[REDIS] {method:green}: {key:cyan}', {
            method: 'SET',
            key: key,
        });

        value = JSON.stringify(value);

        // if an expire time passed
        expire
            ? this.client.set(key, value, 'EX', expire)
            : this.client.set(key, value);

        return this;
    }

    //
    // Get some data
    //
    get(key, callback) {
        return callback(false);
        
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

            callback(data ? JSON.parse(data) : false);
        });
    }

    //
    // Compress some data
    //
    compress(data, callback)
    {
        return callback(data);
        
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
        return callback(data);
        
        if (config.zlibStorage) {
            var data = Buffer.from(data, 'base64');
            zlib.inflate(data, (error, buffer) => {
                if (!error) {
                    return callback(buffer.toString());
                } else {
                    console.error('Error decompressing:', error);
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
