#!/bin/bash
#
#   This Script:
#   - Starts chat server
#
################################

# Start
PROJECT="XIVSync API"

# Display vars
line="--------------------------------------------------"
nl="\n${line}\n"
printf "${nl} ${PROJECT} ${nl}"

sleep 1

echo ">> Stop all"
sudo pm2 stop all

echo ">> Delete"
sudo pm2 delete all

sleep 1

echo ">> Starting chat server"
sudo pm2 start app.js --name API
