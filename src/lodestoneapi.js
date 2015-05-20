/*  --------------------------------------------------
 *  XIVPads.com (v5) - Lodestone API (Javascript)
 *  This runs off: XIVSync.com
 *
 *  > requires jquery.
 *  --------------------------------------------------
 *  API calls will match those of the PHP API, so for
 *  example, you can do:
 *
 *  LodestoneAPI.Search.Character(Name, Server);
 *  LodestoneAPI.Search.Character(123456);
 */
var LodestoneAPI =
{
    logEnabled: false,

    paths:
    {
        sync: 'http://xivsync.com',
        search: '/search/character',
        character: '/character/get',
        achievement: '/achievements/get',
        freecompany: '/freecompany/get',
    },

    // Initialize
    init: function()
    {
        LodestoneAPI.log('init()');

        // check for jquery.
        if (typeof window.jQuery == 'undefined') {
            console.error('Please include https://jquery.com/, this API requires it.');
        }
    },

    // wrapper for ajaxing
    get: function(url, data, callback)
    {
        LodestoneAPI.log('get', url);
        LodestoneAPI.log('get', data);

        $.ajax({
            url: url,
            data: data,
            crossDomain: true,
            dataType: 'json',
            success: function(data)
            {
                LodestoneAPI.log('get > return', data);
                if (data.ok) {
                    return callback(data.data);
                }

                console.error('XIVSync API Error: '+ data.msg);
                return callback(false);
            },
            error: function(data, status, error)
            {
                console.error('Error attempting to ajax to: ' + url);
                console.error(status);
                console.error(error);
                console.error('Please open an issue on Github: https://github.com/viion/XIVPads-LodestoneAPI');
                return callback(false);
            },
        })
    },

    // Logging
    log: function(text, data)
    {
        if (LodestoneAPI.logEnabled) {
            if (data) {
                console.log('[LODESTONE-API]', text, data);
                return;
            }

            console.log('[LODESTONE-API]', text);
        }
    },

    // Search for thingz
    Search:
    {
        Character: function(nameOrId, server, callback, recurrsive)
        {
            if (!callback || typeof callback !== 'function') {
                console.error('Callback function not defined.');
                return;
            }

            if (!nameOrId) {
                console.error('Name or ID is empty');
                return callback(false);
            }

            // if numeric, can just get character
            // else we have to search!
            if ($.isNumeric(nameOrId))
            {
                LodestoneAPI.log('search > character > isNumeric = true =', nameOrId);

                var url = LodestoneAPI.paths.sync + LodestoneAPI.paths.character,
                    data = { lodestone: nameOrId }

                LodestoneAPI.get(url, data, function(data)
                {
                    // if empty
                    if (data.length == 0) {
                        return callback(false);
                    }

                    return callback(data);
                });
            }
            else if (!recurrsive)
            {
                LodestoneAPI.log('search > character > isNumeric = false =', nameOrId);

                var url = LodestoneAPI.paths.sync + LodestoneAPI.paths.search,
                    data = { name: nameOrId, server: server }

                // get
                LodestoneAPI.get(url, data, function(data)
                {
                    // if empty
                    if (data.length == 0) {
                        return callback(false);
                    }


                    // Try match server and name
                    for (var i in data)
                    {
                        var c = data[i];
                        if (c.name == nameOrId && c.world == server && $.isNumeric(c.i));
                        {
                            // recurrsive callback on character using id
                            LodestoneAPI.log('search > character > recurrsion with id:', c.id);
                            LodestoneAPI.Search.Character(c.id, null, callback, true);
                            return;
                        }
                    }

                    LodestoneAPI.log('search > character > no results for: ', nameOrId);
                    return callback(false);
                });
            }
        },

        Achievements: function(id, callback)
        {
            if (!callback || typeof callback !== 'function') {
                console.error('Callback function not defined.');
                return;
            }

            if (!id) {
                console.error('Name or ID is empty');
                return callback(false);
            }

            if ($.isNumeric(id))
            {
                LodestoneAPI.log('search > achievements > isNumeric = true =', id);

                var url = LodestoneAPI.paths.sync + LodestoneAPI.paths.achievement,
                    data = { lodestone: id }

                LodestoneAPI.get(url, data, function(data)
                {
                    // if empty
                    if (data.length == 0) {
                        return callback(false);
                    }

                    return callback(data);
                });
            }
            else
            {
                console.error('ID is not numeric');
                return callback(false);
            }
        },

        FreeCompany: function(id, callback)
        {
            if (!callback || typeof callback !== 'function') {
                console.error('Callback function not defined.');
                return;
            }

            if (!id) {
                console.error('Name or ID is empty');
                return callback(false);
            }

            if (id.length > 1)
            {
                LodestoneAPI.log('search > free company > isNumeric = true =', id);

                var url = LodestoneAPI.paths.sync + LodestoneAPI.paths.freecompany,
                    data = { lodestone: id }

                LodestoneAPI.get(url, data, function(data)
                {
                    // if empty
                    if (data.length == 0) {
                        return callback(false);
                    }

                    return callback(data);
                });
            }
            else
            {
                console.error('ID is too short?');
                return callback(false);
            }
        }
    },
}
LodestoneAPI.init();
