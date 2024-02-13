#!/bin/bash

supervisord -n -c /etc/supervisor/supervisord.conf &
/usr/sbin/php-fpm8.2 -O

