#!/bin/bash

d=`dirname $0`
cd $d

test="calendrier-test"
tick='`'

mysql calendrier --user=motoregister --password=aaXHTtfFURepJ2s5<<EOFMYSQL

	DROP DATABASE IF EXISTS $tick$test$tick;
	CREATE DATABASE $tick$test$tick DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

EOFMYSQL

mysql --user=motoregister --password=aaXHTtfFURepJ2s5 "$test" < lva_cal_test_supercal.sql
