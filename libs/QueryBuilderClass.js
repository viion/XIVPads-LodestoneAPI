//
// Simple mysql query builder
// - XIVSync
//
class QueryBuilderClass
{
    constructor()
    {
        this.parts = [];
    }

    //
    // Reset query
    //
    reset()
    {
        this.parts = [];
    }

    //
    // Get the SQL Statement
    //
    get()
    {
        return this.parts.join(' ');
    }

    //
    // Start a select query (resets)
    //
    select()
    {
        this.reset();
        this.parts.push('SELECT');
        return this;
    }

    //
    // Start an insert (resets)
    //
    insert(table)
    {
        this.reset();
        this.parts.push('INSERT INTO ' + table);
        return this;
    }

    //
    // Start an insert with ignore (resets)
    //
    insertIgnore(table)
    {
        this.reset();
        this.parts.push('INSERT IGNORE INTO ' + table);
        return this;
    }

    //
    // Add columns that are used in insert
    //
    insertColumns(columns)
    {
        this.parts.push('(' + columns.join(',') + ')');
        return this;
    }

    //
    // Add data that will be inserted
    //
    insertData(data)
    {
        var insert = [];

        for (var i in data) {
            insert.push("('" + data[i].join("','") + "')");
        }

        this.parts.push('VALUES ' + insert.join(','));
        return this;
    }

    //
    // Start an update query (resets)
    //
    update(table)
    {
        this.resets();
        this.parts.push('UPDATE ' + table);
        return this;
    }

    //
    // Start an delete query (resets)
    //
    delete()
    {
        this.reset();
        this.parts.push('DELETE');
        return this;
    }

    //
    // Columns used (mainly in select)
    //
    columns(columns)
    {
        this.parts.push(columns.join(','));
        return this;
    }

    //
    // Table to use from
    //
    from(table)
    {
        this.parts.push('FROM ' + table);
        return this;
    }

    //
    // Join a table,
    // format: .join('table_to_join',['main_table.member_id','table_to_join.id'])
    //
    join(table, data, where)
    {
        var string = 'LEFT JOIN '+ table +' ON ('+ data[0] +' = ' + data[1] + ')';
        if (where) {
            string = string + ' AND (' + where[0] + ' = ' + where[1] + ')';
        }

        this.parts.push(string);
        return this;
    }

    //
    // Create a where condition
    //
    where(where, split)
    {
        if(typeof where !== 'string') {
            if (!split) {
                split = 'AND';
            }

            where = where.join(` ${split} `)
        }

        this.parts.push('WHERE ' + where);
        return this;
    }

    //
    // Limit the query
    //
    limit(start, length)
    {
        this.parts.push('LIMIT ' + [start, length].join(','));
        return this;
    }

    //
    // Order the query
    //
    order(column, order)
    {
        this.parts.push('ORDER BY '+ column + ' ' + order.toUpperCase());
        return this;
    }

    //
    // Columns that duplicate
    //
    duplicate(columns)
    {
        var dupe = [];
        for(var i in columns) {
            var col = columns[i];
            dupe.push(`${col}=VALUES(${col})`);
        }

        this.parts.push('ON DUPLICATE KEY UPDATE ' + dupe.join(','));
        return this;
    }
}

// Export it
module.exports = new QueryBuilderClass();
