#!/bin/sh

ROOT_PATH=/home/daze
SERVER=$1
CMD="${ROOT_PATH}/php/bin/php -c ${ROOT_PATH}/php/etc/php.ini /data/htdocs/${SERVER}/public/cli.php ${ROOT_PATH}/nginx/conf/params/${SERVER}.params"
API=CancelOrder

echo $$ > /data/run/patrol_cancel_order.pid

while true
do
    $CMD "/job/$API/patrolCancelOrder"
    sleep 40
done
