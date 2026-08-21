import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        if ($assessment) {
            foreach ($assessment->scores as $score) {
                $this->scores[$score->rubric_item_id] = $score->score;
            }
            $this->calculateTotal();
        } else {"""

content = content.replace("""        if ($assessment) {
            foreach ($assessment->scores as $score) {
                $this->scores[$score->rubric_item_id] = $score->score;
            }
            $this->totalScore = $assessment->total_score;
        } else {""", replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
