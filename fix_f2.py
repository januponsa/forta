import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f2.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "$menScores = $menAssessment->scores()->pluck('score');",
    "$menScores = collect($menAssessment->scores)->pluck('score');"
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
