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

    // detail box
    var $box = $('.item_detail_box');

    // flags
    var flags = '';
    $box.find('ul.eorzeadb_tooltip_ml12 li').each(function() { flags = flags + ' ' + $(this).text().trim(); });
    flags = flags.replace(/\u00a0/g, " ").split(' ').clean('');

    // category
    var category = $box.find('.item_name_area').text().trim().split("\n").clean(''), categoryOffset = 3;
    if (typeof category[categoryOffset] === 'undefined') {
        categoryOffset = 1;
    }

    // sells for
    var sellsfor = $box.find('.popup_w412_body_inner').last().text().trim().split(' ').clean('');

    // data
    var data =
    {
        name: $box.find('.item_name_area h2').text(),
        color: $box.find('.item_name_area h2').attr('class').trim().split(' ').clean('')[1].trim().split('_')[0],
        category: category[categoryOffset].trim(),
        icon: $box.find('.item_ic_box img.sys_nq_element').attr('src').trim(),
        is_unique: $box.find('.item_name_area .rare').length > 0 ? 1 : 0,
        is_untradable: $box.find('.item_name_area .ex_bind').length > 0 ? 1 : 0,
        is_convertible: flags[1] == 'Yes' ? 1 : 0,
        is_projectable: flags[3] == 'Yes' ? 1 : 0,
        is_desynthesizable: flags[5] == 'Yes' ? 1 : 0,
        is_dyeable: flags[7] == 'Yes' ? 1 : 0,
        is_crestable: flags[9] == 'Yes' ? 1 : 0,
        sell_nq: sellsfor[2] ? parseInt(sellsfor[2]) : false,
        sell_hq: sellsfor[6] ? parseInt(sellsfor[6]) : false,
    }

    // related duties
    var $footer = $('.w480_footer.mt10');
    if ($footer.find('h3').text() == 'Related Duties')
    {
        data.duties = [];

        $footer.find('table tbody tr').each(function() {
            var $row1 = $(this).find('td:nth-child(1)'),
                $name = $row1.find('a.db_popup'),
                $kind = $row1.find('.small a:nth-child(1)'),
                $category = $row1.find('.small a:nth-child(2)');

            var id = $name.attr('href').split('/').clean('')[4];

            data.duties.push({
                id: id,
                name: $name.text().trim(),
                url: $name.attr('href'),
                kind_name: $kind.text().trim(),
                kind_url: $kind.attr('href'),
                category_name: $category.text().trim(),
                category_url: $category.attr('href').trim(),
            });
        });
    }

    // related crafting logs
    var $footer = $('.w480_footer.mt10');
    if ($footer.find('h3').text() == 'Crafting Log')
    {
        data.craftinglog = [];

        $footer.find('table tbody tr').each(function() {
            var $row1 = $(this).find('td:nth-child(1)'),
                $row2 = $(this).find('td:nth-child(2)'),
                $row3 = $(this).find('td:nth-child(3)'),
                $name = $row1.find('a');

            var id = $name.attr('href').split('/').clean('')[4];

            data.craftinglog.push({
                id: id,
                icon: $row1.find('img').attr('src'),
                name: $name.text().trim(),
                link: $name.attr('href'),
                recipe_level: parseInt($row2.text().trim()),
                recipe_stars: parseInt($row2.find('.ic_recipe_star_02').length),
                item_level: parseInt($row3.text().trim()),
            });
        });
    }

    console.log(data);

});