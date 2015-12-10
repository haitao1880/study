#!/bin/bash

HOSTNAME="localhost"                                  
PORT="3306"
USERNAME="root"
PASSWORD="password"
DBNAME="rht_admin"                                              
TABLENAME="rha_station"
MYSQL="/usr/bin/mysql"

sql="select id from ${TABLENAME}"

#declare ids = `$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-name`

#declare  ids
ids=`$MYSQL -h${HOSTNAME} -P${PORT} -u${USERNAME} -p${PASSWORD} -D ${DBNAME} -e "${sql}" --skip-column-names`
#echo $ids

for id in $ids
do
{
   php /home/upload/nginxlog/activity/Useractivity.php $id  
}&
done

