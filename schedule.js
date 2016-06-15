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
    //require('tasks/autoAddCharacters').init();
    require('tasks/autoUpdateCharacters').init(0);
    //require('./tasks/autoUpdateCharacters').init(1);
    //require('./tasks/autoUpdateCharacters').init(2);
    log.space();
});
