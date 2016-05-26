#!/bin/sh

prog=/home/taojin/redis/bin/redis-server
pidfile=/data/run/redis.pid
conf=/home/taojin/redis/redis.conf
LAN_IP=`/sbin/ifconfig eth0 | grep 'inet addr' | awk -F':' '{print $2;}' | awk '{print $1;}'`

start()
{
    if [ ! -d "/data/logs" ]; then
        mkdir /data/logs
        chown taojin.taojin /data/logs
    fi

    if [ ! -d "/data/run" ]; then
        mkdir /data/run
        chown taojin.taojin /data/run
    fi

    if [ -z "$LAN_IP" ]; then
        echo "not found lan ip";
        exit 1
    fi

    $prog $conf --bind $LAN_IP --port 6001
}

stop()
{
    kill -TERM `cat $pidfile`
}

case "$1" in
  start)
	start
	;;
  stop)
	stop
	;;
  restart)
	stop
    sleep 1
    start
	;;
  *)
	echo $"Usage: $0 {start|stop|restart}"
    exit 2
    ;;
esac

exit 0
