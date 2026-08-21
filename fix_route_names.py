import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\layouts\admin.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("route('admin.defenses.internship.schedules')", "route('admin.defenses.internship.schedule')")
content = content.replace("route('admin.defenses.internship.mentor-scores')", "route('admin.defenses.internship.mentor-score')")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
