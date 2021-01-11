#!/usr/bin/python3
print("content-type:text/html; charset=UTF-8\n\n")

import webbrowser
import pymysql
import numpy
import os

f= open("val.txt", "w+")
average = float(f.readline())
changed = average + 10
# f.write(changed)
f.close()

print('''

<!DOCTYPE html>
<html style="font-size: 16px;">

  <head>
    <title>Test Page</title>
  </head>

  <body>
    <div style="text-align:center">
      <h4> Value: {before} -> {after} </h4>
    </div>
  </body>

</html>
'''
  .format(
    before = average,
    after = changed
  )
)