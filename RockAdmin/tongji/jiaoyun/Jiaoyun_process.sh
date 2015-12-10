#!/bin/bash
#使用多进程数据分析
#
HOSTNAME="localhost"                                  
PORT="3306"
USERNAME="root"
PASSWORD="Cahw_1MLLqIt"
DBNAME="rht_bus"                                              
TABLENAME="rb_sync_nt"
MYSQL="/usr/bin/mysql"

#查询出没有进行分析的车次
sql="select car_id as id from ${TABLENAME} WHERE t_status = 0"


#declare ids = `$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-name`

#declare  ids
ids=`$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-names`
#echo $ids
for id in $ids
do
{
   php /home/upload/bus/process/Jiaoyun_Process.php $id  
}&
done


