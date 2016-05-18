var functions =
{
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

    clean: function(string, deleteValue) {
        for (var i = 0; i < string.length; i++) {
            if (string[i].trim() == deleteValue) {
                string.splice(i, 1);
                i--;
            }
        }
        return string;
    },

    objLength: function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    }
}

// Export it
module.exports = functions;
