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

echo ">> Stop all"
pm2 stop all

echo ">> Delete"
pm2 delete all

sleep 1

echo ">> Starting: ${PROJECT}"
pm2 start app.js --name API
