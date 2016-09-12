var moment = require('moment'),
    config = require('config'),

    log = require('libs/LoggingObject'),
    functions = require('libs/functions'),
    database = require('libs/DatabaseClass'),
    XIVDBApi = require('libs/XIVDBClass');

class AppCharacterRoleClass
{
	constructor()
    {
		this.View = require('app/app-character-view');
        this.classjobs = {};
	}


	//
    // Initialize tracking
    //
    init(callback)
    {
        // We need items
        XIVDBApi.get('classjobs', (type, classjobs) => {
            this.classjobs = classjobs;
			callback();
        });
    }

	//
	// Simplify classjob data down to ids and values.
	//
	handleClassJobs()
	{
		var temp = {};

		for(var i in this.View.newData.classjobs) {
			var role = this.View.newData.classjobs[i],
                roleId = this.getRoleId(role.name);

            temp[roleId] = {
                id: roleId,
                level: role.level,
                exp: role.exp,
            }
		}

		return temp;
	}

    //
    // Simplify active classjob data down to ids
    //
    handleActiveClassJob()
    {
        var temp = {
            id: this.getRoleId(this.View.newData.active_class.name),
            level: this.View.newData.active_class.level,
        };

        return temp;
    }

	//
    // Get the real ClassJob ID for the role, this is matched by lowercasing the name
    // and matching against the two names, returns false if no match, which will
    // skip any event creation.
    //
    getRoleId(role)
    {
        for(var i in this.classjobs) {
            var row = this.classjobs[i];

            if (row.name_en.toLowerCase() == role.toLowerCase()) {
                return row.id;
                break;
            }
        }

        return false;
    }
}

// Export it
module.exports = new AppCharacterRoleClass();
