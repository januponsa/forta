import sys

file_path = r'c:\Users\userJ\Documents\fortain\tests\Feature\DefensePdfGeneratorTest.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("Lecturer::factory()->create", "Lecturer::create")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
