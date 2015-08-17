var apiItems =
{
    getSearchOptions: function(name)
    {
        return {
            host: 'eu.finalfantasyxiv.com',
            path: '/lodestone/playguide/db/item/?db_search_category=item&category2=&q={name}'.replace('{name}', name),
            port: 80,
        }
    },

    getSearchResults: function($)
    {
        var items = [];
            $rows = $('.table_black #character tbody tr');

        // loop through search results
        $rows.each(function() {
            $dom = $(this);

            var link = $dom.find('.db_popup.highlight').attr('href');
                item_id = link.split('/')[5];

            items.push({
                id: item_id,
                link: link,
                name: $dom.find('td:nth-child(1) .db_popup.highlight').text(),
                img: $dom.find('td:nth-child(1) .ic_reflection_box img').attr('src'),
                kind: $dom.find('td:nth-child(1) .ic_link_txt span a:nth-child(1)').text(),
                category: $dom.find('td:nth-child(1) .ic_link_txt span a:nth-child(2)').text(),
                item_level: parseInt($dom.find('td:nth-child(2)').text()),
                required_level: parseInt($dom.find('td:nth-child(3)').text()),
            });
        });

        return items;
    }
}

// Export it
module.exports = apiItems;