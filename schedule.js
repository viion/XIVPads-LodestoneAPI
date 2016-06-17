require('app-module-path/register');
global.TIMESTAMP = 0;
global.ANALYTICS = require('libs/Analytics').reset('schedule');

// node modules
var setup = require('setup'),
    config = require('config'),

    // libs
    log = require('libs/LoggingObject'),

    // params
    args = process.argv.slice(2),
    spawnProcess = parseInt(args[0]);

global.ANALYTICS.setSpawnProcess(spawnProcess);

// Title
log.title('{msg:purple}', { msg: 'XIVSync Cronjob' });
log.echo('Spawn Process: {spawn:yellow}', { spawn: spawnProcess });

// check spawn process
if (isNaN(spawnProcess)) {
    log.echo('{error:red}', {
        error: 'Please provide a spawn process, eg: "node schedule.js 3"'
    });
    process.exit();
}

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
    autoAddCharacters.init(spawnProcess);
    autoUpdateCharacters.init(spawnProcess);
    autoUpdateAchievements.init(spawnProcess);
});
