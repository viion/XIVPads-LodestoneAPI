# XIVSync

Parsing characters from the [Final Fantasy XIV Lodestone](http://na.finalfantasyxiv.com/lodestone/).

## API

- [Read about XIVSync Service](docs/docs_service.md)
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
