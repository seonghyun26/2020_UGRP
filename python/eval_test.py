#!/usr/bin/python3
print("content-type:text/html; charset=UTF-8\n\n")

import webbrowser
import pymysql
import numpy
import os

db = pymysql.connect(
  user='scooker', 
  passwd='eatupugrp', 
  host='localhost',
  db='testdb1', 
  charset='utf8'
)


cursor = db.cursor(pymysql.cursors.DictCursor)
sql = "SELECT * FROM `mark1_test` where record_date BETWEEN '2020-10-20 16:38:00' AND '2020-10-20 16:39:59'";
cursor.execute(sql)
results = cursor.fetchall()
db.close();

f = open("../../value.txt", "r")
average = f.readline()
standard = float(average)
f.close()

x_datas=[]
y_datas=[]
z_datas=[]
for result in results:
  x_datas.append(result['acc_x'])
  y_datas.append(result['acc_y'])
  z_datas.append(result['acc_z'])

sum = float(0)
sum_plus = 0
sum_minus = 0
num = 0
w1 = 1
w2 = 1

for data in x_datas:
  sum += (standard - data) * (standard - data)
  sum_plus += w1*(standard + 0.01 - data)*(standard + 0.01 - data)
  sum_minus += w2*(standard - 0.01 - data)*(standard - 0.01 - data)
  num += 1
sum = sum/num
sum_plus = sum_plus/num
sum_minus = sum_minus/num


min_ = min(sum, sum_plus, sum_minus)
if min_ == sum_plus:
  standard += 0.01
elif min_ == sum_minus:
  standard -= 0.01


print('''

<!DOCTYPE html>
<html style="font-size: 16px;">

  <head>
    <title>Test Page</title>
  </head>

  <body>
    <div style="text-align:center">
      <h1>Machine Learning Test Page</h1>
      <h3 >Precision: {precision} (Smaller, Better) </h3>
      <h4> Value: {before} -> {changed} </h4>
      <button type="button" class="button blue" onclick="location.href='./test/acc.php'">
        Acc Graph  
      </button>
      <br>
      X datas: {datas_show}
    </div>
  </body>

</html>
'''
  .format(
    precision = sum,
    before = float(average),
    changed = standard,
    datas_show = x_datas
  )
)