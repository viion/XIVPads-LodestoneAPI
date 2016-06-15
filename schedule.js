require('app-module-path/register');
global.TIMESTAMP = 0;

// node modules
var setup = require('setup'),
    log = require('libs/LoggingObject');

// Title
log.title('{msg:purple}', {
    msg: 'XIVSync Cronjob'
});

// ------------------------------------------------
// Activate Tasks
// ------------------------------------------------

setup.init(() => {
    // Auto add characters
    //require('tasks/autoAddCharacters').init();

    // Auto update characters
    //require('tasks/autoUpdateCharacters').init(0);

    // Auto update achievements
    require('./tasks/autoUpdateAchievements').init(0);

});
