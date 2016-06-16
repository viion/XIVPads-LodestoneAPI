require('app-module-path/register');
global.TIMESTAMP = 0;
global.ANALYTICS = require('libs/Analytics').reset('schedule');

// node modules
var setup = require('setup'),
    config = require('config'),
    log = require('libs/LoggingObject');

// Title
log.title('{msg:purple}', {
    msg: 'XIVSync Cronjob'
});

// ------------------------------------------------
// Activate Tasks
// ------------------------------------------------
setup.init(() => {
    global.ANALYTICS.record('setup', 'Complete');

    // Modules
    var autoAddCharacters = require('tasks/autoAddCharacters'),
        autoUpdateCharacters = require('tasks/autoUpdateCharacters'),
        autoUpdateAchievements = require('tasks/autoUpdateAchievements');

    // Auto add characters
    if (config.settings.autoAddCharacters.enabled) {
        for (var i = 0; i < config.settings.autoAddCharacters.spawn; i++) {
            autoAddCharacters.init(i);
        }
    }


    // Auto update characters
    if (config.settings.autoAddCharacters.enabled) {
        for (var i = 0; i < config.settings.autoAddCharacters.spawn; i++) {
            autoUpdateCharacters.init(i);
        }
    }

    // Auto update achievements
    if (config.settings.autoAddCharacters.enabled) {
        for (var i = 0; i < config.settings.autoAddCharacters.spawn; i++) {
            autoUpdateAchievements.init(i);
        }
    }
});
