# playgta5-web-rewritten — local execution

This project runs a web version of the game inside a Docker container. Apache
serves the static files, and PHP implements the read endpoint required by the
frontend.

The procedures below are the same on Windows, Linux, and macOS. You only need
Docker installed and must run the commands from this project's directory.

## Assets notice

The `mirror/playgta5.com` directory is not distributed with this project. It
may contain copyrighted files, and I am not responsible for that content: I
only used the files that were available to create this rewrite. I do not know
the origin or the detailed contents of every file; I only know that the files
present work with this implementation.

Obtain the assets at your own responsibility from a source you are authorized
to use. Do not request, redistribute, or include this directory in public
copies of this project. See [`docs/ASSETS.md`](./docs/ASSETS.md) for
identification and verification.

## Requirements

- Docker Desktop on Windows/macOS or Docker Engine on Linux;
- Docker Compose v2, usually available as `docker compose`;
- the assets placed in `mirror/playgta5.com/`;
- port `8081` available on the computer.

Verify the installation with:

```text
docker --version
docker compose version
```

If the assets are not mounted, the container still starts and serves a visible
`missing-mirror.html` diagnostic page at the root URL.

## Required structure

Before starting, the directory should look like this:

```text
rewritten/
├── docker-compose.yml
├── public/
└── mirror/
    └── playgta5.com/
        ├── index.html
        ├── b/
        └── data/
```

The contents of `mirror/playgta5.com/` are not part of this repository and
must be obtained separately.

## Start

Open any terminal — PowerShell, Command Prompt, a Linux shell, or macOS
Terminal — and change to this project's `rewritten` directory. For example:

```text
cd path/to/rewritten
```

Run:

```text
docker compose up --build
```

The first start may take some time because Docker needs to download the
`php:8.4-apache` image and build the application. Then open this address in
your browser:

```text
http://localhost:8081/
```

By default, the server is published only on `127.0.0.1`.

To start it in the background:

```text
docker compose up --build --detach
```

## Stop the server

If Compose is running in the foreground, press `Ctrl+C`. If it is running in
the background, use:

```text
docker compose down
```

## Development and rebuilding

The `public/` and `mirror/playgta5.com/` directories are mounted as read-only
volumes. Changes to public files become available without rebuilding the image.
Refresh the browser page to load the changes.

If you change the `Dockerfile`, `docker-compose.yml`, `apache-vhost.conf`, or
the entrypoint script, rebuild and restart:

```text
docker compose up --build
```

To view service logs:

```text
docker compose logs --follow
```

To check the container status:

```text
docker compose ps
```

## Common problems

### The container shows the missing-assets page

Confirm that these paths exist relative to the project directory:

```text
mirror/playgta5.com/index.html
mirror/playgta5.com/b/
mirror/playgta5.com/data/
```

The container checks for `index.html` before starting Apache. If it is missing,
the root URL serves `missing-mirror.html` with instructions instead of trying
to load the game.

### Port 8081 is already in use

Stop the process using the port or change the mapping in
`docker-compose.yml`. For example, to use port `8082`:

```yaml
ports:
  - "127.0.0.1:8082:80"
```

Then use:

```text
http://localhost:8082/
```

### The browser shows an old version

Perform a hard refresh and confirm that you are using the correct port. The
persistent game-data cache is explained in
[`docs/CLIENT-CACHE.md`](./docs/CLIENT-CACHE.md).

## Removing containers and the image

To remove the containers and network created by Compose:

```text
docker compose down
```

To also remove the locally built image, list the images and remove the one
corresponding to this project using Docker commands. Do not remove the
`mirror/playgta5.com/` directory because it is the local asset volume.

For the architecture and endpoint specifications, see the
[`docs/`](./docs/) directory.
