#!/usr/bin/env bash

# Docker Desktop на Windows отдаёт смонтированную рабочую копию как root:root
# с правами 755, и appuser не может писать ни в storage, ни в vendor. Владелец,
# выставленный изнутри контейнера, сохраняется, поэтому на старте всё чужое
# переходит к appuser. На Linux владелец уже совпадает и find ничего не трогает.
if [[ "$(id -u)" != "0" ]]; then
    return 0 2>/dev/null || exit 0
fi

find /var/www \
    \( -path /var/www/vendor -o -path /var/www/node_modules \) -prune \
    -o ! -user appuser -exec chown appuser:appgroup {} +

for dir in vendor node_modules; do
    if [[ -d "/var/www/${dir}" && "$(stat -c %U "/var/www/${dir}")" != "appuser" ]]; then
        chown -R appuser:appgroup "/var/www/${dir}"
    fi
done
