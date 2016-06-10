var functions = require('../libs/functions');

var apiDatabaseRecipes =
{
    getUrl: function(type, string, page) {
        var urls =
        {
            search: '/lodestone/playguide/db/recipe/?db_search_category=recipe&category2=&q={string}&page={page}'.replace('{string}', string).replace('{page}', page),
            recipe: '/lodestone/playguide/db/recipe/{string}/'.replace('{string}', string),
        }

        return urls[type];
    },

    getSearch: function($) {
        var results = [];
        $('.table_black #character tbody tr').each(function() {
            $dom = $(this);

            var link = $dom.find('.db_popup.highlight').attr('href');
                recipe_id = link.split('/')[5];

            results.push({
                id: recipe_id,
                link: link,
                name: $dom.find('td:nth-child(1) .db_popup.highlight').text(),
                img: $dom.find('td:nth-child(1) .ic_reflection_box img').attr('src'),
                kind: $dom.find('td:nth-child(1) .ic_link_txt span a:nth-child(1)').text(),
                recipe_book_name: $dom.find('td:nth-child(1) .ic_link_txt span.recipe_book_name').text(),
                recipe_level: parseInt($dom.find('td:nth-child(2)').text()),
                recipe_number_stars: parseInt($dom.find('.ic_recipe_star_02').length),
                required_level: parseInt($dom.find('td:nth-child(3)').text()),
            });
        });

        var data = {
            paging: {
                start: parseInt($('.current_list .show_start').eq(0).text().trim().replace(/,/g, '')),
                end: parseInt($('.current_list .show_end').eq(0).text().trim().replace(/,/g, '')),
                total: parseInt($('.current_list .total').eq(0).text().trim().replace(/,/g, '')),
            },
            version: $('.area_footer .right').text().split(':')[1].trim(),
            error: $('.error_msg').length > 0 ? $('.error_msg').text().trim() : false,
            results: results,
        };

        data.paging.pages = Math.ceil(data.paging.total / (data.paging.end - (data.paging.start - 1)));

        return data;
    },

    getData: function($) {

        // detail box
        var $box = $('.item_detail_box');

        var $ingredients = $box.find('.table_black tr:nth-child(3) div.inr_table_outer.mb10 .ic_link_txt a');

        var items = [];
        $ingredients.each(function() {
            $ingredient = $(this);

            name = $ingredient.text().split('(')[0].trim();
            quantity = parseInt($ingredient.text().split('(')[1]) > 1 ? parseInt($ingredient.text().split('(')[1]) : 1;

            items.push({
                'id': $ingredient.attr('href').trim().split('/')[5],
                'name': name,
                'quantity': quantity
            });
        });

        // data
        var data =
        {
            name: $box.find('h2.item_name').text(),
            icon: $box.find('.item_ic_box img.sys_nq_element').attr('src').trim(),
            items: items
        };

        return data;
    },
}

// Export it
module.exports = apiDatabaseRecipes;
