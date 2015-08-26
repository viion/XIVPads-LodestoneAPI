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

// get
$.get('dev-html.html', function(data) {
    $('.dev').html(data);
    // - - - - - - - - - - - - - - - - - - - -

    var data = [];
    $('.table_elements_com_fc tr').each(function() {
        $node = $(this);

        var freecompany = {
            id: $node.find('.ic_freecompany_box a').attr('href').split('/')[3],
            emblums: {
                1: $('.ic_crest_64 img').eq(0).attr('src').trim(),
                2: $('.ic_crest_64 img').eq(1).attr('src').trim(),
                3: $('.ic_crest_64 img').eq(2).attr('src').trim(),
            },
            grand_company: $node.find('.player_name_area .groundcompany_name').text().trim(),
            name: $node.find('.player_name_area h4 a').text().trim(),
            server: $node.find('.player_name_area h4 span').text().replace('(', '').replace(')', '').trim(),
        };

        data.push(freecompany);
    });

    console.log(data);
});