# XIVSync Service

The XIVSync service allows you to query information from Lodestone and retrieve it in clean JSON. You do not need to worry about formatting, scraping or maintaining any code. You just ask for something and you get it.

The XIVSync service is in Tokyo, Japan, this allows the server to parse Lodestone extremely fast. You may notice slowdowns when trying to do get information if your server is in the US or EU, so here are some alternatives

- Need Lodestone homepage info? Try: http://xivdb.com/assets/lodestone.json
- Need Dev Posts? Try: http://xivdb.com/assets/devtracker.json
- Need Character information? (Coming soon to XIVDB!)
- Need game data information? Try https://github.com/viion/XIVDB-API

## About

XIVSync is a **FREE** service that parses data from the [Final Fantasy XIV Lodestone](http://na.finalfantasyxiv.com/lodestone/). It has two purposes; provide a consistent data format via a relay and provide persistent tracking. The service is hosted in Tokyo so it has a very minimal hop rate to the lodestone, this provides a very fast interaction. This service is open source and you can run your own if you would like, it runs on NodeJS.

### API: Relay

Parsing Lodestone yourself can be a slow process, especially from a US/EU server due to the distance a response has to travel. It can also be quite a big task to parse the pages and maintain a consistency for your applications. The relay aims to provide you a direct response from the lodestone for a specific piece of content and always be in the same format. This allows you to focus on your application and not getting the data.

Features:
- Parses Lodestone in real-time, the data is parsed the moment the API call is requested
- Provides a consistent format, data always comes back the same
- Response in JSON

To get started using the API, follow:

- [API: Character](docs/api_characters.md)
- [API: Free Company](docs/api_freecompany.md)
- [API: Linkshells](docs/api_linkshells.md)
- [API: Forums](docs/api_forums.md)
- [API: Lodestone](docs/api_lodestone.md)
- [API: Database](docs/api_database.md)

### API: Persistent Tracking

One of the great features of XIVSync is it's tracking. To help extend profiles on XIVDB this service tracks characters progress such as EXP changes or gear equipped, showing more than what is viewable with a basic snapshot of a characters profile. Using the persistent tracking will take data already pre-parsed and will include additional benefits.

Features:
- More data, includes EXP/Level history, gear and other statistics.
- Faster, does not parse Lodestone in real time (it auto-updates characters in the background)
- Response in JSON
- You can query XIVDB, a US based server!

To get started using the API, follow:

- [XIVDB: Character](https://github.com/viion/XIVDB-API) (WIP, not yet ready).
