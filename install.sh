#!/bin/bash
bash scripts/installreq.sh
cp -R etc/caddy/* /etc/caddy
cp -R usr/share/* /usr/share
service caddy restart

caddy reload --config /etc/caddy/Caddyfile
echo "Caddy Web Installed..."
