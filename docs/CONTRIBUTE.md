# Contributing to the API

This application parses Lodestone using a module that has a JQuery similar syntax, if you know JQuery, you can contribute to this project very easily!

Make sure you get yourself setup, follow the information on the [Getting Setup](SETUP.md) doc.

---

## Parsing a page

So, you want to start parsing a page. Maybe the quest information? Go find a quest on Lodestone. I will use the quest: [Close To Home](http://eu.finalfantasyxiv.com/lodestone/playguide/db/quest/1da75996ae6/)

We need to get the source code as we cannot parse directly against the live URL from our test/develop page, an important note is that we do not want the whole source code as it includes scripts and other useless stuff that will try redirect us, We just want the sections between:

- `<!-- contents -->`
- down to
- `<!-- //#contetnts-->`

If you use the **Inspect** feature of your browser, you can just copy the element:
```html
<div id="contents" class="clearfix">
```

Paste the source code into the file: `web/dev-html.html`

Open the dev tool: [http://xivsync.dev/dev](http://xivsync.dev/dev)

This tool grabs the source code we pasted into `dev-html.html` and injects it into the page allowing us to parse it. We don't need to worry about formatting or styles as we just want raw data, so ignore how it looks. :)

## Developing Code

Open the file: `dev.js`

This is where we will write out code, you would begin writing your code right after the line: `// WRITE YORU CODE HERE`. A JQuery element variable is ready for you to use: `$dom`

### Getting data

If we want to get the quest name, we first need to find where it lies within the HTML. Using your browsers **Inspect** tool is the best way to find it.

![Inspect View](http://i.imgur.com/5r8aDw9.png)

From this you can see it is quite far down the dom tree. When selecting data we want to be specific but not too specific that our code is long, also try to ignore the logical naming SE use, you'll often find footers in headers and bodies in footers, its fun!

To get this quest name, I would use the following dom structure:

```js
var name = $dom.find('.eorzea_head .quest_details_head > span').text();
```

Which would return: `Close to Home`

### Continue

Continue getting data and have a play around, use normal JQuery Syntax!

Once you have done, move onto looking at Integrating it into the API, if you find that difficult, then you can post your `dev.js` file to the [GitHub Repository](https://github.com/viion/XIVPads-LodestoneAPI) by creating a new issue and posting the code, I can then add it for you. :)

### Integrating

(WIP)
