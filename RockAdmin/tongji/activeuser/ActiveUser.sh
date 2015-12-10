#!/bin/bash
#使用多线程解析日志
#
HOSTNAME="localhost"                                  
PORT="3306"
USERNAME="root"
PASSWORD="Cahw_1MLLqIt"
DBNAME="rht_admin"                                              
TABLENAME="rha_station"
MYSQL="/usr/bin/mysql"
#查询数据库
sql="select id from ${TABLENAME}"

#declare ids = `$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-name`

#declare  ids
ids=`$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-names`
#echo $ids
for id in $ids
do
{
   #php /home/upload/nginxlog/dolog.php $id  
   php /home/upload/nginxlog/activeuser/ActiveUser.php $id 
}&
done
