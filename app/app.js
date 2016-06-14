var appCharacter = require('app/app-character.js'),
    appFreeCompany = require('app/app-freecompany.js'),
    appLinkshell = require('app/app-linkshell.js');

//
// Apps class, links to other app entities.
//
class AppClass
{
    constructor()
    {
        this.Character = appCharacter;
        this.FreeCompany = appFreeCompany;
        this.Linkshell = appLinkshell;
    }
}

// Export it
module.exports = new AppClass();
