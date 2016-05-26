#!/bin/sh

prog=/home/taojin/nginx/sbin/nginx
pidfile=/data/run/nginx.pid
conf=/home/taojin/nginx/conf/nginx.conf

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

    $prog -c $conf
}

stop()
{
    kill -TERM `cat $pidfile`
}

reload()
{
    kill -HUP `cat $pidfile`
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
  reload)
	reload
	;;
  *)
	echo $"Usage: $0 {start|stop|restart|reload}"
    exit 2
    ;;
esac

exit 0
