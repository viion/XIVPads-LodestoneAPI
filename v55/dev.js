// - - - - - - - - - - - - - - - - - - - -
// This dev file is just so I can get a
// bunch of correct jquery stuff, copied
// into their respective parsing scripts
// - - - - - - - - - - - - - - - - - - - -
Array.prototype.clean = function(deleteValue) {
    for (var i = 0; i < this.length; i++) {
        if (this[i].trim() == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

// prevent issues
function ldst_strftime(time, stamp) {}
$.get('dev-html.html', function(data) {
    $('.dev').html(data);
    // - - - - - - - - - - - - - - - - - - - -


    // - - - - - - - - - - - - - - - - - - - -
    console.log(data);
});