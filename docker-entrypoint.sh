#!/bin/sh
set -eu

if [ -f /mirror/index.html ]; then
    export MIRROR_AVAILABLE=1
else
    export MIRROR_AVAILABLE=0
    echo "WARNING: assets are missing from /mirror; serving missing-mirror.html." >&2
    echo "Place the authorized assets in mirror/playgta5.com/ and restart." >&2
fi

exec "$@"
