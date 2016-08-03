# Characters

Search and get information about characters.

---

## /characters/search

Search for a character

### GET Parameters

> `name={name}`

> `server={server}`

### Examples

Search on all servers for characters with **premium** anywhere in their name.
> `/characters/search?name=premium`

Search all characters on the server **shiva**, server name is case-insensitive.
> `/characters/search?server=shiva`

### Response Format

```
{
    "paging": {
        "start": 1,
        "end": n,
        "total": n,
        "pages": 1
    },
    "error": false,
    "results": []
}
```


All search responses return a paging and results object entry. Some will also include a error entry, this is any error that is shown on Lodestone. For example when searching all characters with no parameters, lodestone limits your results to 1000 characters. This returns an error informing this has happened.

The paging entry is an object containing: `start`, `end`, `total`, `pages`. The `start` and `end` is the current list of characters. So this could be 51-100 when on page 2 of characters. The `total` is the total results returned and the `pages` is the total number of pages (calculated).

SquareEnix do not provide class/job names on their search results pages. It is not possible right now to know what class is what. Future dev will look into to seeing if the icons are static and can be matched to a class/job

---

## /characters/get/{id}

Get data on a character

### GET Parameters

> restrict={field},{field},{field}
- Will restrict the response to the specified fields.

> ignore={field},{field},{field}
- Will ignore the specified fields from the response.


### Examples

Get the data for the character **730968** (me!)
> /characters/get/730968

Get only the **name**, **server** and **title** for the character **730968**
> /characters/get/730968?restrict=name,server,title

Ignore **minions** and **mounts** from the data for the character **730968**.
> /characters/get/730968?ignore=minion,mounts


---

## /persistent/characters/get/{id}

Get persistent character data. This is data that is stored on XIVSync and avoids parsing. You will get some additional information using this command including the cache date.

### GET Parameters

n/a

### Examples

Get the data for the character **730968**
> /persistent/characters/get/730968

### Notes:

The response includes character data in the attribute: `data`, the value has been compressed with zlib and base64 decoded. To extract it you can use the following techniques:

> PHP

```php
$response = ... your query logic ...

$response['data'] = base64_decode($response['data']);
$response['data'] = gzuncompress($response['data']);
// $data is now a json string.
```

> NodeJS v5 or higher

```js
var zlib = require('zlib');

var response = ... your query logic ...
var zlibData = Buffer.from(response.data, 'base64');
zlib.inflate(zlibData, (error, buffer) => {
    if (!error) {
        var characterData = buffer.toString();
    } else {
        console.error('Error decompressing:', error);
    }
});
```


---

## /characters/get/{id}/achievements/

Get characters achievements (summary)

---

## /characters/get/{id}/achievements/all

Get characters achievements (all of them)

---

## /characters/get/{id}/achievements/{kind}

Get characters achievements for a specified catagory
