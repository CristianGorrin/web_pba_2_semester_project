import os, shutil, re

ignore_dir   = ['lib/unittest/', 'build/']
ignore_files = ['phpproj', 'md']

remove = [
    re.compile(r'#region TODO remove - it only used for unit testing(.|\n)*?#endregion', re.MULTILINE),
    re.compile(r'\/\*\*(.|\n)*?\*\/', re.MULTILINE),
    re.compile(r'\/\/(.*)?', re.MULTILINE),
    re.compile(r'^\s*$', re.MULTILINE)
]

base_dir   = os.getcwd() + '/'
output_dir = base_dir + 'build/'

if os.path.isdir(output_dir):
    shutil.rmtree(output_dir);
    pass
os.mkdir(output_dir);

map        = []
dir_buffer = [base_dir]


while len(dir_buffer) > 0:
    at = dir_buffer.pop()
    
    for x in os.listdir(at):
        x = at + x
        if os.path.isdir(x):
            x += '/'

            if x[len(base_dir):] in ignore_dir: continue

            dir_buffer.append(x)
            map.append(x)
            pass

        if os.path.isfile(x):
            temp = x.split('.')

            if len(temp) < 2: continue
            if temp[len(temp) - 1] in ignore_files: continue

            map.append(x)
            pass
        pass
    pass

for x in map:
    if os.path.isdir(x):
        os.mkdir(output_dir + x[len(base_dir):])   
    else:
        file = ""
        with open(x) as f:
            file = f.read()
            pass
        
        for y in remove:
            file = re.sub(y, '', file)
            pass

        output = open(output_dir + x[len(base_dir):], 'w+')
        output.write(file)
    pass
