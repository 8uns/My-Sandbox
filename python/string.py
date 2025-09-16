# join and convertion string type
name = 'Rafli J Kasim'
age = 28

message = 'My name is ' + name + " and i am " + str(age) + " years old "
# print(message)



# get length of string
# print(len(str(age)))

# get string with index
# print(name[1])
# print(name[2:5])

# string methods
print('normal     : ' + message)
print('upper      : ' + message.upper())
print('lower      : ' + message.lower())
print('title      : ' + message.title())
print('capitalize : ' + message.capitalize())
print('strip      : ' + message.strip())
print('replace    : ' + message.replace('i am', "i'm"))
print(f'count      : {message.count("i")}')
print(f'find       : {message.find('name')}')
