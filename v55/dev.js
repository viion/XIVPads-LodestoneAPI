// - - - - - - - - - - - - - - - - - - - -
// This dev file is just so I can get a
// bunch of correct jquery stuff, copied
// into their respective parsing scripts
// - - - - - - - - - - - - - - - - - - - -

// prevent issues
function ldst_strftime(time, stamp) {}

// get
$.get('dev-html.html', function(data) {
    $('.dev').html(data);
    // - - - - - - - - - - - - - - - - - - - -


    var $box = $('.item_detail_box');
    var data =
    {
        name: $box.find('.item_name_area h2').text(),
        color: $box.find('.item_name_area h2').attr('class').replace("\n", '').split(' ')[4].trim().split('_')[0],
        category: $box.find('.item_name_area').text().split("\n")[16].trim(),
    }



    console.log(data);

});