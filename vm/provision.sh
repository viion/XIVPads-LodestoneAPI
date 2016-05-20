#!/usr/bin/env bash
cd /vagrant
USER=vagrant
sudo locale-gen en_GB.UTF-8

# Settings
mysqlPassword="xivsync"
mysqlDbUser="xivsync"
mysqlDbPass="xivsync"
mysqlDbName="xivsync"

# Set global mysql user/pass
echo "mysql-server mysql-server/root_password password $mysqlPassword" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $mysqlPassword" | debconf-set-selections

# Software
sudo apt-get update
sudo apt-get install -y curl mysql-server git htop zip

# Mysql
sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "CREATE USER '$mysqlDbUser'@'localhost' IDENTIFIED BY '$mysqlDbPass';"
sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "CREATE DATABASE $mysqlDbName;"
sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "GRANT ALL PRIVILEGES ON *.* TO '$mysqlDbUser'@'localhost';"
sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "FLUSH PRIVILEGES;"

# Install node
sudo curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
sudo apt-get -y install nodejs

# Map IP Table
sudo iptables -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-port 3000

# Run node app
npm install pm2 -g
bash /home/vagrant/run


echo "-----------------"
echo "open: xivsync.dev"
echo "-----------------"
