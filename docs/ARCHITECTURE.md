# Architecture

## Overview

The browser runs the application. The container provides the files and
implements only the dynamic operation required for data loading.

```text
browser
├── index.html, JavaScript, WASM, workers, and shaders
├── WebGPU
├── IndexedDB for user data
└── OPFS for downloaded data blocks
        │
        │ HTTP
        ▼
Apache + PHP 8.4
├── public application files
├── read-only assets from /mirror
└── POST /data/batch
```

## Image and volumes

The image is based on `php:8.4-apache`. The `Dockerfile` enables `mod_headers`
and `mod_rewrite`, copies a fallback version of `public/` into the image, and
creates the `/mirror` mount point.

During normal Compose use, these volumes replace the copied files:

```text
./public                  -> /var/www/html/public:ro
./mirror/playgta5.com     -> /mirror:ro
```

The assets are not copied into the image or modified by the container.

## Routing

Apache's `DocumentRoot` is `/var/www/html/public`.

[`public/.htaccess`](../public/.htaccess):

- routes `/` to the original `index.html` inside `/mirror`;
- routes `/index.html` to the same file;
- routes `/data/batch` to [`public/batch.php`](../public/batch.php);
- routes all other paths to the internal `/_assets/` alias, which points to
  `/mirror`.

This allows the frontend to continue using the original paths even though the
assets are mounted outside the application's public root.

## Headers

Apache adds these headers to responses:

```text
Cross-Origin-Opener-Policy: same-origin
Cross-Origin-Embedder-Policy: require-corp
Cross-Origin-Resource-Policy: same-origin
Accept-Ranges: bytes
```

They are required for the origin isolation used by `SharedArrayBuffer` and
workers. `.wasm` files are served as `application/wasm`, and `.js` files as
`text/javascript`.
