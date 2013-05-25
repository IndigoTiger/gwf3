#!/bin/bash
# -*- coding: utf-8 -*-

usage() {
	echo "usage: $(basename $0) <config_lamb.php> <Lamb_Config.php> <www> <backup> <music>"
}

if [ $# -eq 0 -o "$1" == "--help" -o "$1" == "-h" ]; then
	usage
	exit 1
fi

# chroot
if [ $3 -eq 1 ]; then
	cd ../../../../
fi

# backup db
if [ $4 -eq 1 ]; then
	cd www/protected
	./db_backup.sh
	cd ../..
fi

# paths
if [ $1 = "dev" ]; then
	config="www/protected/config_lamb_dev.php"
elif [ $1 = "gwf" ]; then
	config="www/protected/config.php"
else
	config=$1
fi

if [ $2 = "dev" ]; then
	lamb="Lamb_Config_dev.php"
else
	lamb=$2
fi

#exec bot
if ! php core/module/Lamb/lamb_bin/lamb_main.php $config $lamb; then
	# Oops
	case $5 in
		"once")
			# play music once
			mplayer core/module/Lamb/lamb_bin/wecken.mp3
			;;
		"yes")
			# play music every 20 seconds
			while [ 1 ]
			do
				mplayer core/module/Lamb/lamb_bin/wecken.mp3
				sleep 20
			done
			;;
	esac
fi