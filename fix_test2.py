import sys

file_path = r'c:\Users\userJ\Documents\fortain\tests\Feature\DefensePdfGeneratorTest.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    """Student::create([
            'user_id' => $studentUser->id,
            'nim' => '2023012345',
            'name' => 'Alfred Gerald Thendiwijaya',
            'email' => 'alfred@example.com',
            'semester' => 'Ganjil 2025/2026'
        ]);""",
    """Student::factory()->create([
            'user_id' => $studentUser->id,
            'nim' => '2023012345',
            'name' => 'Alfred Gerald Thendiwijaya',
            'email' => 'alfred@example.com',
        ]);"""
)
content = content.replace(
    """Lecturer::create([
            'user_id' => $spvUser->id,
            'nip' => '0301018501',
            'name' => 'Dr. Budi Rahardjo, S.Kom., M.T.',
            'email' => 'budi@example.com'
        ]);""",
    """Lecturer::factory()->create([
            'user_id' => $spvUser->id,
            'nip' => '0301018501',
            'name' => 'Dr. Budi Rahardjo, S.Kom., M.T.',
            'email' => 'budi@example.com'
        ]);"""
)
content = content.replace(
    """Lecturer::create([
            'user_id' => $exmUser->id,
            'nip' => '0302028602',
            'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.',
            'email' => 'anita@example.com'
        ]);""",
    """Lecturer::factory()->create([
            'user_id' => $exmUser->id,
            'nip' => '0302028602',
            'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.',
            'email' => 'anita@example.com'
        ]);"""
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
