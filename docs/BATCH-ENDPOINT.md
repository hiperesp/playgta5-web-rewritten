# `POST /data/batch` endpoint

## Purpose

The frontend needs to read multiple byte ranges from large files. Instead of
opening one request for every range, the worker groups multiple ranges and
sends a JSON payload to the server.

Example:

```http
POST /data/batch
Content-Type: application/json
```

```json
[
  ["common/data/file.rpf", 0, 4095],
  ["common/data/other.rpf", 100, 199]
]
```

The limits are inclusive: the second number is the first byte and the third
number is the last requested byte. PHP reads the segments, concatenates the
bytes in the same order, and returns `200 OK` with
`application/octet-stream`.

This is not an HTTP `206 Partial Content` response. It is a binary-read
protocol over `POST`.

## Response

The `X-Run-Lengths` header reports how many bytes were returned for each range:

```text
X-Run-Lengths: 4096,100
```

The client uses these lengths to split the concatenated response again.
`POST /data/batch?gz=1` returns the body compressed with gzip.

## Validation

[`public/batch.php`](../public/batch.php) enforces:

- `POST` as the only accepted method;
- a request body of up to 1 MiB;
- no more than 1000 ranges;
- a concatenated response of up to 64 MiB;
- relative paths without backslashes or null bytes;
- existing files inside `/mirror/data`;
- rejection of traversal and symlinks outside the data root.

## Other reads

Normal reads use `GET` with the `Range` header, handled directly by Apache.
The frontend may also try a static batch at
`/data/batchc/<hash>.bin`; if it does not exist, it uses
`POST /data/batch`.

The optional `/log` and `/shot` diagnostic endpoints are not part of this
implementation because normal loading does not depend on them.
