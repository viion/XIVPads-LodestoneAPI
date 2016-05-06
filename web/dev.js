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
function ldst_strftime(time, stamp) {}
// - - - - - - - - - - - - - - - - - - - -
$.get('/web/dev-html.html', function(data) {
    var $dom = $('.dev'); $dom.html(data);
    // - - - - - - - - - - - - - - - - - - - -
    // Your dev code goes here, and is then
    // copied inside an api/api-<entity> file
    // once you've got it parsing eveything
    //
    // Use the $dom variable as the parent
    // for scraping the data.
    //
    // Use normal jquery syntax
    // - - - - - - - - - - - - - - - - - - - -





    // - - - - - - - - - - - - - - - - - - - -
    // End
    // - - - - - - - - - - - - - - - - - - - -
});
