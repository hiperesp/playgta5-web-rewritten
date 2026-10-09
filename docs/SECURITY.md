# Security and operation

- The asset volume is mounted read-only.
- Apache listens on `127.0.0.1:8081` by default.
- The PHP endpoint rejects traversal, absolute paths, backslashes, and
  symlinks escaping `/mirror/data`.
- The batch endpoint limits request size, the number of ranges, and response
  size.
- There is no database, session, or user state on the server.
- Saves and settings remain in the browser's local storage.

This configuration is intended for local use. If the bind is changed to expose
the service on a network or the internet, authentication, limits, logs, rate
limiting, HTTPS, and the asset distribution policy must be reviewed.
