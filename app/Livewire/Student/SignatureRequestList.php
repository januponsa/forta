<?php

namespace App\Livewire\Student;

use App\Models\SignatureRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

class SignatureRequestList extends Component
{
    use WithPagination;

    #[Layout('layouts.student')]
    public function render()
    {
        $requests = SignatureRequest::where('student_id', auth('student')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('livewire.student.signature-request-list', compact('requests'));
    }
}
