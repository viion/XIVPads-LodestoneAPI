require('app-module-path/register');
global.TIMESTAMP = 0;
global.START_RANGE = parseInt(process.argv[2]);
global.START_TASK = process.argv[3];

// node modules
var setup = require('setup'),
    config = require('config'),
    log = require('libs/LoggingObject');

// Title
log.title('{msg:purple}', { msg: 'XIVSync Cronjob' });
log.echo('Start Range: {spawn:yellow}', { spawn: global.START_RANGE });

// check spawn process
if (isNaN(global.START_RANGE)) {
    log.echo('{error:red}', {
        error: 'Please provide a spawn process, eg: "node schedule.js 3 autoAddCharacters"'
    });
    process.exit();
}

// check start task is correct
var validTasks = ['autoAddCharacters', 'autoUpdateCharacters', 'autoUpdateAchievements'];
if (validTasks.indexOf(global.START_TASK) == -1) {
    log.echo('{error:red}', {
        error: 'Start task invalid, it should be one of: ' + validTasks.join(', ')
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

    if (typeof global.START_TASK == 'undefined') {
        log.echo('{error:red}', {
            error: 'No task provided, please include either: autoAddCharacters, autoUpdateCharacters, autoUpdateAchievements'
        });
        log.echo('{error:red}', {
            error: 'Example: node schedule.js 3 autoAddCharacters'
        });
        process.exit();
    }

    log.echo('Running Task: {task:yellow}', { task: global.START_TASK });

    // Auto add characters
    if (global.START_TASK == 'autoAddCharacters') {
        autoAddCharacters.init(global.START_RANGE);
    }

    // Auto update characters
    if (global.START_TASK == 'autoUpdateCharacters') {
        autoUpdateCharacters.init(global.START_RANGE);
    }

    // Auto update achievements
    if (global.START_TASK == 'autoUpdateAchievements') {
        autoUpdateAchievements.init(global.START_RANGE);
    }
});
