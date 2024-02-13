#!/bin/bash
/usr/sbin/php-fpm8.2 -O

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf