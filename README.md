## 12th April 2017
- If you need a PHP API: https://github.com/viion/lodestone-php
- If you just want rest actions https://github.com/xivdb/api (Characters only)

I will at some point fix this library but it's not a priority, otherwise please do a PR!

---

## This is broken as of March 31st 2017 due to Lodestone updates http://eu.finalfantasyxiv.com/lodestone/special/update_log/

---

# NodeJS Lodestone Parser for FFXIV

Parsing characters from the [Final Fantasy XIV Lodestone](http://na.finalfantasyxiv.com/lodestone/).

## API

- [API: Character](docs/api_characters.md)
- [API: Free Company](docs/api_freecompany.md)
- [API: Linkshells](docs/api_linkshells.md)
- [API: Forums](docs/api_forums.md)
- [API: Lodestone](docs/api_lodestone.md)
- [API: Database](docs/api_database.md)


## Helping out

Helping to build this API is very simple, basic Javascript/JQuery knowledge is all you need for parsing, and some MySQL understanding will help in the persistent areas (not required)

If you are interested, check out:
- [Getting A Local Environment Setup](docs/docs_setup.md)
- [Contributing to the API](docs/docs_contribute.md) (Once you've parsed a page)

## Cronjob auto scripts

Each schedule task has 2 arguments:

- Start Offset
- Action

Test like so:

- `node schedule 0 autoAddCharacters`
- `node schedule 0 autoUpdateAchievements`
- `node schedule 0 autoUpdateCharacters`
