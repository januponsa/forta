import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Http\Controllers\MentorDocumentController.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """namespace App\Http\Controllers;

use App\Models\DefenseCase;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MentorDocumentController extends Controller
{
"""

content = content.replace("""namespace App\Http\Controllers;

use App\Models\DefenseCase;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MentorDocumentController extends Controller
{""", replacement)

content = content.replace("$this->authorize(", "Gate::authorize(")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
