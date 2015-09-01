// This is code taken from lodestones events page
// http://img.finalfantasyxiv.com/lds/pc/global/js/common/lodestone.plugin.timezoned_data.js?1428044661
var lodestoneFunction = {

    timezone_info_from_op: function(op) {
        var data;

        switch ( op.method ) {
            case 'serial':
                data = lodestoneFunction.timezone_info_by_serial(op);
                break;
            case 'point':
                data = lodestoneFunction.timezone_info_by_point(op);
                break;
        }

        return data;
    },

    timezone_info_by_serial: function(op) {
        var start_dt = lodestoneFunction.get_date(op.start);
        var end_dt   = lodestoneFunction.get_date(op.end);

        var dt_times = [];
        if ( start_dt && end_dt && start_dt <= end_dt ) {
            dt_times.push(start_dt.getTime() / 1000);
            var move_dt = new Date(start_dt.getTime());
            move_dt.setDate(move_dt.getDate() + 1);
            var last_dt_time = start_dt.getTime();
            while ( move_dt <= end_dt ) {
                if ( (move_dt.getTime() - last_dt_time) == 86400000 ) {
                    // normal 1 day
                    var last_dt = dt_times[dt_times.length-1];
                    if ( typeof last_dt === 'object' ) {
                        last_dt.skip++;
                    }
                    else {
                        dt_times.push({'skip': 1});
                    }
                }
                else {
                    // unnormal 1 day
                    // maybe summer time change
                    dt_times.push(move_dt.getTime() / 1000);
                }
                last_dt_time = move_dt.getTime();
                move_dt.setDate(move_dt.getDate() + 1);
            }
        }
        var data = {
            method: 'serial',
            dt_times: dt_times
        };

        return data;
    },

    timezone_info_by_point: function(op) {
        var dt = lodestoneFunction.get_date(op);
        return {
            method: 'point',
            epoch: dt.getTime() / 1000,
            year: dt.getFullYear(),
            month: dt.getMonth() + 1,
            date: dt.getDate()
        };
    },

    get_date: function(op) {
        var dt;

        switch ( typeof op ) {
            case 'string':
                dt = lodestoneFunction.get_date_from_string(op);
                break;
            case 'object':
                dt = lodestoneFunction.get_date_from_object(op);
                break;
        }

        return dt;
    },

    get_date_from_string: function(op) {
        var dt;
        switch ( op ) {
            case 'today':
                dt = new Date();
                dt.setHours(0);
                dt.setMinutes(0);
                dt.setSeconds(0);
                dt.setMilliseconds(0);
                break;
        }
        return dt;
    },

    get_date_from_object: function(op) {
        if ( op instanceof Date ) {
            return op;
        }
        var dt;
        if ( op.base ) {
            dt = lodestoneFunction.get_date(op.base);
            if ( op.add ) {
                dt.setDate(dt.getDate() + op.add);
                if ( op.right_limit ) {
                    var limit_dt = lodestoneFunction.get_date(op.right_limit);
                    if ( limit_dt < dt ) {
                        dt = limit_dt;
                    }
                }
                if ( op.left_limit ) {
                    var limit_dt = lodestoneFunction.get_date(op.left_limit);
                    if ( limit_dt > dt ) {
                        dt = limit_dt;
                    }
                }
            }
        }
        else if ( op.epoch_day ) {
            dt = new Date(op.epoch_day * 1000);
            dt.setHours(0);
            dt.setMinutes(0);
            dt.setSeconds(0);
            dt.setMilliseconds(0);
        }
        else if ( op.ymd ) {
            dt = new Date(op.year, op.month-1, op.date);
            dt.setHours(0);
            dt.setMinutes(0);
            dt.setSeconds(0);
            dt.setMilliseconds(0);
        }
        return dt;
    }
}


// Export it
module.exports = lodestoneFunction;