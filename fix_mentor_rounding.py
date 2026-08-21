import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """    private function calculateTotal()
    {
        $sum = 0;
        $count = 0;
        $hasDecimal = false;
        foreach ($this->scores as $itemId => $val) {
            if (is_numeric($val) && $val >= 0 && $val <= 100) {
                $sum += (float) $val;
                $count++;
                // Check if input has decimal
                if (strpos((string)$val, '.') !== false || strpos((string)$val, ',') !== false) {
                    $hasDecimal = true;
                }
            }
        }
        
        if ($count > 0) {
            $avg = $sum / $count;
            $this->totalScore = $hasDecimal ? round($avg, 2) : round($avg, 0);
        } else {
            $this->totalScore = 0;
        }
    }"""

# Find the existing calculateTotal function and replace it
pattern = re.compile(r'\s*private function calculateTotal\(\)\s*\{.*?\}', re.DOTALL)
content = pattern.sub('\n' + replacement, content, count=1)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
