#!/bin/sh
screen -d -m php artisan queue:listen --tries=100
