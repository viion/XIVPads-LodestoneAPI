var log = require('./log'),
    setup = require('./setup');

log.title('{msg:purple}', { msg: 'XIVSync Cronjob' });

// ------------------------------------------------
// Activate Tasks
// ------------------------------------------------

setup.init(() => {
    require('./tasks/autoAddCharacters').init();
    log.space();
});
