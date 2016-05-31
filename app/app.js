var appCharacter = require('./app-character.js'),
    appFreeCompany = require('./app-freecompany.js'),
    appLinkshell = require('./app-linkshell.js');

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
