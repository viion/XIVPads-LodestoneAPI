var log = require('./log');
log.title('{msg:purple}', { msg: 'XIVSync Cronjob' });

// ------------------------------------------------
// Activate Tasks
// ------------------------------------------------
require('./tasks/autoAddCharacters').init();
