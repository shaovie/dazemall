basepath=$(cd `dirname $0`; pwd)

nohup sh ${basepath}/patrol_cancel_order.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/cancel_order.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/sendwxmsg.sh.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/kucun_alarm.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/timingmprice.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/timingupdown.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/report.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/wxeventasync.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/asyncdbopt.sh $1 >/dev/null 2>&1 &
