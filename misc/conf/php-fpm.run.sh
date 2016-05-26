#!/bin/sh

prog=/home/taojin/php/sbin/php-fpm
pidfile=/data/run/php-fpm.pid

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

    $prog -c /home/taojin/php/etc/php.ini -y /home/taojin/php/etc/php-fpm.conf
}

stop()
{
    kill -TERM `cat $pidfile`
}

reload()
{
    kill -USR2 `cat $pidfile`
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

