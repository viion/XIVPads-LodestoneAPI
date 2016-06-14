var moment = require('moment');

var STR_PAD_LEFT = 1;
var STR_PAD_RIGHT = 2;
var STR_PAD_BOTH = 3;

var functions =
{
    //
    // Get the time from moment
    //
    time: function()
    {
        return parseInt(moment().format('X'));
    },


    //
    // Output the memory usage
    //
    memory: function()
    {
        return process.memoryUsage().heapUsed;
    },

    //
    // Convert memory to a human readable format
    //
    memoryToHuman: function(bytes)
    {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },

    //
    // Replace all "find" with "replace" in a string
    //
    replaceAll: function(string, find, replace)
    {
        var regex = new RegExp(find, 'g');
        return string.replace(regex, replace);
    },

    //
    // Insert something into a string at s specific position
    //
    insertIntoString: function(string, insert, index, fromend)
    {
        string = string.toString();
        if (fromend) { index = string.length - index; }
        return (string.slice(0,index) + insert + string.slice(index));
    },

    //
    // Format something into ucwords (same as PHP function)
    //
    ucwords: function(string)
    {
        return (string + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
    },

    //
    // Detects if something is int.
    //
    isInt: function(n) {
       return n % 1 === 0;
    },

    //
    // Clean array, can delete a specific value
    //
    clean: function(string, deleteValue) {
        for (var i = 0; i < string.length; i++) {
            if (string[i].trim() == deleteValue) {
                string.splice(i, 1);
                i--;
            }
        }
        return string;
    },

    //
    // Get the length of an object
    //
    objLength: function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    },

    //
    // Object to string
    //
    objString: function(obj) {
        return JSON.stringify(obj);
    },

    //
    // Convert object to array
    //
    objToArray: function (obj) {
        return Object.keys(obj).map(key => obj[key]);
    },

    //
    // String padding, mostly used for logging
    //
    padding: function(str, len, pad, dir)
    {
        if (typeof(len) == "undefined") { var len = 0; }
        if (typeof(pad) == "undefined") { var pad = ' '; }
        if (typeof(dir) == "undefined") { var dir = STR_PAD_RIGHT; }

        if (str && len + 1 >= str.length)
        {
            switch (dir)
            {
                case STR_PAD_LEFT:
                    str = Array(len + 1 - str.length).join(pad) + str;
                    break;

                case STR_PAD_BOTH:
                    var right = Math.ceil((padlen = len - str.length) / 2);
                    var left = padlen - right;
                    str = Array(left+1).join(pad) + str + Array(right+1).join(pad);
                    break;

                default:
                    str = str + Array(len + 1 - str.length).join(pad);
                    break;

            } // switch
        }

        return str;
    },

    //
    // Get a random number
    //
    randomNumber(min, max)
    {
        return Math.floor((Math.random() * max) + min);
    },
}

// Export it
module.exports = functions;
