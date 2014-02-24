#!/bin/bash

sudo apt-get update --fix-missing &&
apt-get install -y php5-mysql sqlite3 php5-sqlite curl &&
cd /tmp/ &&
wget http://delasallep.free.fr/eip/eip.zip &&
unzip eip && cp -r /tmp/eip /var/www/. &&
wget http://delasallep.free.fr/eip/hr &&
cp /tmp/hr /etc/apache2/sites-available/. &&
a2ensite hr &&
a2enmod rewrite &&
chown -R www-data:www-data /var/www &&
wget http://www.python.org/ftp/python/3.3.2/Python-3.3.2.tar.bz2 &&
tar xjf Python-3.3.2.tar.bz2 &&
cd Python-3.3.2 &&
./configure && make && make install &&
wget http://delasallep.free.fr/eip/tornado-3.1.tar.gz &&
tar zxvf tornado-3.1.tar.gz &&
cd tornado-3.1 &&
python3.3 setup.py build &&
python3.3 setup.py install &&
service apache2 restart &&
rm -rf /var/www/eip/app/logs/* /var/www/eip/app/cache/*  &&
chmod -R 777 /var/www/eip/app/logs  /var/www/eip/app/cache &&
echo "Deployment completed !" &&
cd /tmp &&
wget http://delasallep.free.fr/eip/deb &&
cat deb >> /etc/apt/sources.list &&
curl http://www.dotdeb.org/dotdeb.gpg | apt-key add - &&
apt-get -y update &&
apt-get -y dist-upgrade &&
aptitude -y purge php5-suhosin &&
apt-get -y install mysql-server &&
apt-get -y install php5-mysqlnd &&
echo "upgrade to php 5.4" &&
cp /etc/php5/apache2/php.ini tmp &&
sed "s/;date.timezone =/date.timezone = 'Europe\/Paris'/" <tmp > /etc/php5/apache2/php.ini &&
cp /etc/php5/cli/php.ini tmp &&
sed "s/;date.timezone =/date.timezone = 'Europe\/Paris'/" <tmp > /etc/php5/cli/php.ini &&
echo "php.ini updated for cli and apache2" &&
wget http://delasallep.free.fr/eip/parameters.yml &&
mv parameters.yml /var/www/eip/app/config/parameters.yml &&
echo "parameters.yml copied" &&
cd /var/www/eip &&
php composer.phar update &&
php app/console doctrine:database:create &&
php app/console doctrine:schema:update --force &&
php app/console assets:install web --symlink &&
rm -rf /var/www/eip/app/logs/* /var/www/eip/app/cache/*  &&
echo "Database initialization completed" &&
php app/console hr:initSchema &&
php app/console hr:initGame &&
php app/console hr:initForum &&
echo "mysql initialization completed" &&
cd src/EIP/HRBundle/Utils/ &&
chmod +x switchDb.sh &&
./switchDb.sh &&
mkdir /var/www/eip/src/EIP/HRBundle/db
chown -R www-data:www-data /var/www &&
chmod 775 /var/www/eip/src/EIP/HRBundle/db &&
cd /var/www/eip &&
rm -rf /var/www/eip/app/logs/* /var/www/eip/app/cache/*  &&
chmod -R 777 /var/www/eip/app/logs  /var/www/eip/app/cache &&
php app/console doctrine:database:create &&
php app/console doctrine:schema:update --force &&
php app/console hr:initSchema &&
php app/console hr:initGame &&
php app/console hr:initForum &&
echo "sqlite initialization completed" &&
cd src/EIP/HRBundle/Utils &&
./switchDb.sh &&
rm -rf /var/www/eip/app/logs/* /var/www/eip/app/cache/*  &&
echo "Back to mysql" &&
chown -R www-data:www-data /var/www/ &&
echo 'chown www-data done -- su www-data & launch scripts'
#start the chat server
#start the scripts