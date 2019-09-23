#!/usr/bin/python
# -*- coding: UTF-8 -*-
import os
import datetime
rootdir =  "D:/wamp64/www/GENSMO/files/";
list = os.listdir(rootdir);
sum = 0;
for file in list:
	if file.split('.')[-1] == 'xlsx':
		dateStr = file.split('-')[0]
		d = datetime.datetime.strptime(dateStr,'%Y%m%d')
		if (datetime.datetime.now() - d).days > 30:
			os.remove(rootdir+file)
			sum = sum + 1;

with open('D:/wamp64/www/GENSMO/files/clean_log.txt','a') as f:
	f.write(datetime.datetime.now().strftime('%Y-%m-%d') + " 清理了" + str(sum) + "个文档\n")