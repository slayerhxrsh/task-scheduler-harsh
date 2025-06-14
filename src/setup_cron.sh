#!/bin/bash

# Absolute path to PHP executable
PHP_PATH="/usr/bin/php"

# Absolute path to cron.php
CRON_FILE="/home/ubuntu/task-scheduler-slayerhxrsh-main/src/cron.php"

# Register cron job to run every hour
(crontab -l 2>/dev/null; echo "0 * * * * $PHP_PATH $CRON_FILE") | crontab -
