This application parses Lodestone using a module that has a JQuery similar syntax, if you know JQuery, you can contribute to this project very easily!

Once you have your node server running, go to: `http://localhost:3838/dev` and you should see a small dev page that includes some "broke lodestone html". This page loads in the file: `/web/dev-html.html` which contains html from the lodestone. (You cannot pull in html from the live web page using javascript due to domain restrictions).

To get started, go to the page you would like to code against, eg: http://eu.finalfantasyxiv.com/lodestone/playguide/db/duty/bf316aa7eee/

Open up Dev Tools on your browser, and go to the "Elements" section. You want to find the element: `<div id="contents" class="clearfix">`, this will have a `<!-- contents -->` tag before it.

Right click and copy the entire element (which should include the html within it) and paste it into `/web/dev-html.html`.

You can then code against that html page by opening up `/web/dev.js`. Use the `$dom` variable to parse the html and get the information you need, look at some of the code in the `/api/api-*.js` files to get an idea of the structure and some techniques you can use.

Once you have some code, integrate it into a function in the `api/api*.js` files (or make your own function) and build your routes in `app.js` and `/api/api.js`.
