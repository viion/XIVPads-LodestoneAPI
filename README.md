# XIVSync API

Used for parsing the Final Fantays XIV Lodestone.

## Getting started locally

- `npm install`
- `cp config.js.dist config.js`
- `node app.js`

## Setup

This API runs on io.js (cannot use NodeJS until it merges with io.js), it also uses pm2 to stay alive.

- `npm install pm2 -g`
- `npm install`
- `cp config.js.dist config.js`
- `nano config.js` modify config as required.
- `bash run.sh `

## Documentation

All documentation can be found on xivsync.com

## _dev

This folder contains some simple html/js with a setup to create the parse Javascript code in jQuery. This translates to Cheeriojs 1:1 so very easy to patch and create parsing.

