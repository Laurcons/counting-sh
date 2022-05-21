#!/bin/bash

confKey=`date +%s%N`

# create confirmation file
touch "/home/[REDACTED]/counting_public/$confKey"

# get more data
userName=`whoami`

# call curl
response=`curl -s -X POST -F "user=$userName" -F "confKey=$confKey" http://localhost/[REDACTED]/counting/`

# delete conf file
rm "/home/[REDACTED]/counting_public/$confKey"

# parse response
IFS=',' read -ra PARTS <<< "$response"

error=${PARTS[0]}

# print messages
if [ $error -eq 0 ]; then
	currentNum=${PARTS[1]}
	prevNum=$(($currentNum - 1))
	lastUserName=${PARTS[2]}
	info=`cat /etc/passwd | egrep "$lastUserName" | awk -F: '{ match($6, /gr([0-9]*)/, grm); print $1 " - " $5 " - " grm[1] }'`
	echo "Count registered. You're ${currentNum}."
	echo "$prevNum: $info"
	echo "$currentNum: $userName"
elif [ $error -eq 3 ]; then
	echo "You already counted. Wait for someone else!"
else
	echo "A mysterious error occurred."
	echo "$error: ${PARTS[1]}"
fi
