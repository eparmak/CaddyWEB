#!/bin/bash
rsync -av --exclude='config.php' usr/share/caddyweb/ /usr/share/caddyweb
