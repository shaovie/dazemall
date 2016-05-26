if [ `whoami` != 'root' ]; then
    echo 'must root!'
    exit 1
fi

APPUSER=daze
yum -y install pcre-devel openssl-devel zlib-devel
yum -y install libxml2-devel curl-devel  mysql-devel libmcrypt-devel libpng-devel libjpeg-devel freetype-devel

##For redis
echo never > /sys/kernel/mm/transparent_hugepage/enabled
sysctl vm.overcommit_memory=1
echo "" >> /etc/sysctl.conf
echo "# for redis" >> /etc/sysctl.conf
echo "vm.overcommit_memory = 1" >> /etc/sysctl.conf

##For php7
sysctl vm.nr_hugepages=128
echo "" >> /etc/sysctl.conf
echo "# for php7" >> /etc/sysctl.conf
echo "vm.nr_hugepages=128" >> /etc/sysctl.conf

[ ! -d /data ] && mkdir /data && chown ${APPUSER}.${APPUSER} /data
[ ! -d /data/logs ] && mkdir /data/logs && chown ${APPUSER}.${APPUSER} /data/logs
[ ! -d /data/run  ] && mkdir /data/run  && chown ${APPUSER}.${APPUSER} /data/run
[ ! -d /data/rdb  ] && mkdir /data/rdb  && chown ${APPUSER}.${APPUSER} /data/rdb
[ ! -d /data/htdocs ] && mkdir /data/htdocs  && chown ${APPUSER}.${APPUSER} /data/htdocs
[ ! -d /data/htdocs/localhost ] && mkdir /data/htdocs/localhost  && chown ${APPUSER}.${APPUSER} /data/htdocs/localhost

##rc.local
if [ -z "`grep '^#For daze' /etc/rc.local`" ]; then
    echo "" >> /etc/rc.local
    echo "#For daze" >> /etc/rc.local
    echo "echo 8192 > /proc/sys/net/ipv4/tcp_max_syn_backlog" >> /etc/rc.local
    echo "echo 20480 > /proc/sys/net/ipv4/tcp_max_tw_buckets" >> /etc/rc.local
    echo "echo 1 > /proc/sys/net/ipv4/tcp_tw_reuse" >> /etc/rc.local
    echo "echo 0 > /proc/sys/net/ipv4/tcp_tw_recycle" >> /etc/rc.local
    echo "echo 1 > /proc/sys/net/ipv4/tcp_syncookies" >> /etc/rc.local
    echo "" >> /etc/rc.local
    echo "sh /home/${APPUSER}/shell/redis.run.sh start" >> /etc/rc.local
    echo "sh /home/${APPUSER}/shell/php-fpm.run.sh start" >> /etc/rc.local
    echo "sh /home/${APPUSER}/shell/nginx.run.sh start" >> /etc/rc.local
fi

echo 8192 > /proc/sys/net/ipv4/tcp_max_syn_backlog
echo 20480 > /proc/sys/net/ipv4/tcp_max_tw_buckets
echo 1 > /proc/sys/net/ipv4/tcp_tw_reuse
echo 0 > /proc/sys/net/ipv4/tcp_tw_recycle
echo 1 > /proc/sys/net/ipv4/tcp_syncookies

##crontab
crontab -l > /tmp/crontab.tmp
if [ -z "`grep '^#For daze' /tmp/crontab.tmp`" ]; then
    echo "#For daze" >> /tmp/crontab.tmp
    echo "30 0 * * * /usr/sbin/ntpdate tick.ucla.edu tock.gpsclock.com ntp.nasa.gov timekeeper.isi.edu usno.pa-x.dec.com &" >> /tmp/crontab.tmp
    echo "0  0 * * * sh /home/${APPUSER}/shell/nginx_log_division.sh &" >> /tmp/crontab.tmp
    crontab /tmp/crontab.tmp
fi
rm -f /tmp/crontab.tmp

