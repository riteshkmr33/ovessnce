from django.db import connection, transaction
# For execute Raw Queries #####
def sql_select(sql):
    cursor = connection.cursor()
    cursor.execute(sql)
    results = cursor.fetchall()
    list = []
    i = 0
    for row in results:
        dict = {} 
        field = 0
        while True:
           try:
                dict[cursor.description[field][0]] = str(results[i][field])
                field = field +1
           except IndexError as e:
                break
        i = i + 1
        list.append(dict) 
    return list