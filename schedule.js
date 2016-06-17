require('app-module-path/register');
global.TIMESTAMP = 0;
global.SPAWN = parseInt(process.argv[2]);
global.ANALYTICS = require('libs/Analytics').reset('schedule');

// node modules
var setup = require('setup'),
    config = require('config'),
    log = require('libs/LoggingObject');

// Title
log.title('{msg:purple}', { msg: 'XIVSync Cronjob' });
log.echo('Spawn Process: {spawn:yellow}', { spawn: global.SPAWN });

// check spawn process
if (isNaN(global.SPAWN)) {
    log.echo('{error:red}', {
        error: 'Please provide a spawn process, eg: "node schedule.js 3"'
    });
    process.exit();
}

// ------------------------------------------------
// Activate Tasks
// ------------------------------------------------
setup.init(() => {
    // Modules
    var autoAddCharacters = require('tasks/autoAddCharacters'),
        autoUpdateCharacters = require('tasks/autoUpdateCharacters'),
        autoUpdateAchievements = require('tasks/autoUpdateAchievements');

    // Auto add characters
    autoAddCharacters.init(global.SPAWN);
    autoUpdateCharacters.init(global.SPAWN);
    autoUpdateAchievements.init(global.SPAWN);
});
