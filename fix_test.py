import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\tests\Feature\DefensePdfGeneratorTest.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "'name' => 'Alfred Gerald Thendiwijaya',",
    "'name' => 'Alfred Gerald Thendiwijaya',\n            'email' => 'alfred@example.com',"
)
content = content.replace(
    "'name' => 'Dr. Budi Rahardjo, S.Kom., M.T.'",
    "'name' => 'Dr. Budi Rahardjo, S.Kom., M.T.',\n            'email' => 'budi@example.com'"
)
content = content.replace(
    "'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.'",
    "'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.',\n            'email' => 'anita@example.com'"
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
