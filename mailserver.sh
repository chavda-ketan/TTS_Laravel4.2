#!/bin/sh
screen -d -m php artisan queue:listen
