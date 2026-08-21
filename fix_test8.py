import sys

file_path = r'c:\Users\userJ\Documents\fortain\tests\Feature\DefensePdfGeneratorTest.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    """Submission::create([
            'student_id' => $student->id,
            'form_id' => 1,
            'nim' => '2023012345',
            'title' => 'Sistem Rekomendasi Restoran Berbasis AI',
            'status' => 'approved',
            'submission_type' => 'internship_report'
        ]);""",
    """Submission::factory()->create([
            'student_id' => $student->id,
            'title' => 'Sistem Rekomendasi Restoran Berbasis AI',
            'status' => 'approved',
        ]);"""
)
content = content.replace(
    """DefenseCase::create([""",
    """DefenseCase::factory()->create(["""
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
