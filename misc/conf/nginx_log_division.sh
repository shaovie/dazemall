#/bin/bash              
                
logs_path=/data/logs/   
pid_path=/data/run/nginx.pid
                
[ -d "$logs_path/bak" ] || mkdir $logs_path/bak
for log_name in `ls $logs_path/*.access.log $logs_path/*.error.log` ; do
    log_name=`basename $log_name`  
    mv ${logs_path}${log_name} ${logs_path}bak/${log_name}_$(date --date="LAST DAY" +"%Y-%m-%d")
done                    

kill -USR1 `cat ${pid_path}`
