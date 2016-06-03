#!/bin/sh

ROOT_PATH=/home/daze
SERVER=$1
CMD="${ROOT_PATH}/php/bin/php -c ${ROOT_PATH}/php/etc/php.ini /data/htdocs/${SERVER}/public/cli.php ${ROOT_PATH}/nginx/conf/params/${SERVER}.params"
API=TimingUpDown

echo $$ > /data/run/timingupdown.pid

while true
do
    $CMD "/job/$API/doit"
    sleep 1
done
