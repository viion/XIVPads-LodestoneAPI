//
// Apps class, links to other app entities for simple global access
//
class AppClass
{
    constructor()
    {
        this.Character = require('app/app-character');
        this.FreeCompany = require('app/app-freecompany');
        this.Linkshell = require('app/app-linkshell');
        this.Achievements = require('app/app-achievements');
    }
}

// Export it
module.exports = new AppClass();
