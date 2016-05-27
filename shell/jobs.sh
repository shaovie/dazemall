basepath=$(cd `dirname $0`; pwd)

nohup sh ${basepath}/patrol_cancel_order.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/cancel_order.sh $1 >/dev/null 2>&1 &
