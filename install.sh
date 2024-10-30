#!/bin/bash
bash scripts/installreq.sh
cp -R etc/caddy/* /etc/caddy
cp -R usr/share/* /usr/share
service caddy restart
caddy reload --config /etc/caddy/Caddyfile
echo "Caddy Web Installed...\n"
echo "Change /etc/php/x.x/fpm/pool.d/www.conf (listen = 127.0.0.1:9000)\n and systemctl restart phpX.x-fpm\n"
echo "Restart caddy service (service caddy restart)"
