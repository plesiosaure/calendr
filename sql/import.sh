#!/bin/bash

d=`dirname $0`
cd $d

prod="calendrier"
tick='`'

mysql calendrier --user=motoregister --password=aaXHTtfFURepJ2s5<<EOFMYSQL

	DROP DATABASE IF EXISTS $tick$prod$tick;
	CREATE DATABASE $tick$prod$tick DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

EOFMYSQL

mysql --user=motoregister --password=aaXHTtfFURepJ2s5 "$prod" < lva_cal.sql
