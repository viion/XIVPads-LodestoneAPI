# XIVSync Service

A tracking and relay parser for FFXIV Characters, built and maintained by XIVDB.

## About

XIVSync is a **FREE** service that parses data from the [Final Fantasy XIV Lodestone](http://na.finalfantasyxiv.com/lodestone/). It has two purposes; provide a consistent data format via a relay and provide persistent tracking. The service is hosted in Tokyo so it has a very minimal hop rate to the lodestone, this provides a very fast interaction. This service is open source and you can run your own if you would like, it runs on NodeJS.

### API: Relay

Parsing Lodestone yourself can be a slow process, especially from a US/EU server due to the distance a response has to travel. It can also be quite a big task to parse the pages and maintain a consistency for your applications. The relay aims to provide you a direct response from the lodestone for a specific piece of content and always be in the same format. This allows you to focus on your application and not getting the data.

Features:
- Parses Lodestone in real-time, the data is parsed the moment the API call is requested
- Provides a consistent format, data always comes back the same
- Response in JSON


### API: Persistent Tracking

One of the great features of XIVSync is it's tracking. To help extend profiles on XIVDB, this service tracks characters progress such as EXP changes or gear equipped, showing more than what is viewable with a basic snapshot of a characters profile. Using the persistent tracking will take data already pre-parsed and will include additional benefits.

Features:
- More data, includes EXP/Level history, gear and other statistics.
- Faster, does not parse Lodestone in real time (it auto-updates characters in the background)
- Response in JSON
