# Browser cache

The game's data cache is stored in the user's browser, not in Docker and not
in the `public/` directory.

## OPFS

[`io_worker.js`](../mirror/playgta5.com/io_worker.js) uses the Origin Private
File System (OPFS) to create:

```text
OPFS/
└── gamedata/
    ├── store.bin
    └── journal.bin
```

`store.bin` contains the downloaded binary blocks. `journal.bin` maps the file
ID and block number to the corresponding position in `store.bin`. The original
files do not appear individually in OPFS.

The worker downloads only the requested blocks, stores them in OPFS, and
reuses them for later reads. The manifest version identifies the cached
content; when it changes, the storage is reset.

If OPFS is unavailable, the worker uses an in-memory cache limited to
approximately 48 MiB, which disappears when the page is closed.

## User data

Saves and settings are stored separately in IndexedDB:

```text
IndexedDB
└── gta5-userdata
    └── files
```

This is not the asset cache.

## Service Worker

There is no active Service Worker in this version. The HTML only removes old
registrations and `gamedata` caches that may have been created by previous
versions.

## Origin

Storage is specific to the origin. For example, these addresses do not share
the same OPFS:

```text
http://localhost:8081
http://localhost:8000
https://example.test
```

To check storage usage in the browser console:

```javascript
const estimate = await navigator.storage.estimate();
console.log(estimate.usage, estimate.quota);
```
