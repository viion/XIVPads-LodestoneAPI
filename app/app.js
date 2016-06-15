//
// Apps class, links to other app entities for simple global access
//
class AppClass
{
    constructor()
    {
        this.Character = require('app/app-character.js');
        this.FreeCompany = require('app/app-freecompany.js');
        this.Linkshell = require('app/app-linkshell.js');
        this.Achievements = require('app/app-achievements.js')
    }
}

// Export it
module.exports = new AppClass();
