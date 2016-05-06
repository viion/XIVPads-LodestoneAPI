# XIVSync API

A NodeJS application to parse the: Final Fantasy XIV Lodestone: http://eu.finalfantasyxiv.com/lodestone/

## Getting started locally

- `npm install`
- `cp config.js.dist config.js`
- `node app.js`

## Setup

Requirements:

- This is a NodeJS application, please use Node 4.2 or higher.
- Require PM2, install using: `npm install pm2 -g`


Getting setup:

Open up a terminal/command window and run:

- `npm install`
- `cp config.js.dist config.js`

Modify the `config.js` file as require for your environment. Note that if you are just looking to parse Lodestone and not store character data, then set the option `persistent` to false.

Then you should be ready to run it,

- Local/Dev: `node app.js`
- Server/Live: `bash start.sh`

## Documentation

All documentation can be found on xivsync.com

## Development

This application parses Lodestone using a module that has a JQuery similar syntax, if you know JQuery, you can contribute to this project very easily!

Once you have your node server running, go to: `http://localhost:3838/dev` and you should see a small dev page that includes some "broke lodestone html". This page loads in the file: `/web/dev-html.html` which contains html from the lodestone. (You cannot pull in html from the live web page using javascript due to domain restrictions).

To get started, go to the page you would like to code against, eg: http://eu.finalfantasyxiv.com/lodestone/playguide/db/duty/bf316aa7eee/

Open up Dev Tools on your browser, and go to the "Elements" section. You want to find the element: `<div id="contents" class="clearfix">`, this will have a `<!-- contents -->` tag before it.

Right click and copy the entire element (which should include the html within it) and paste it into `/web/dev-html.html`.

You can then code against that html page by opening up `/web/dev.js`. Use the `$dom` variable to parse the html and get the information you need, look at some of the code in the `/api/api-*.js` files to get an idea of the structure and some techniques you can use.

Once you have some code, integrate it into a function in the `api/api*.js` files (or make your own function) and build your routes in `app.js` and `/api/api.js`.
