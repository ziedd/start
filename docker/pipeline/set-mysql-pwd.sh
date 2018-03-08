/usr/sbin/mysqld &
sleep 10
mysql -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('challouf50');"
