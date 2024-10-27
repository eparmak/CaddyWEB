#!/bin/bash
apt update
apt install -y php-fpm
apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
apt update
apt install -y caddy

echo "Change /etc/php/x.x/fpm/pool.d/www.conf (listen = 127.0.0.1:9000)\n and systemctl restart phpX.x-fpm"
