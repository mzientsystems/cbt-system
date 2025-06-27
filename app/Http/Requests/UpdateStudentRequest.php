
<?php

// app/Http/Requests/UpdateStudentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Add proper authorization logic
    }

    public function rules()
    {
        $userId = $this->route('student')->id;
        $profileId = $this->route('student')->profile->id ?? null;

        return [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => 'nullable|string|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'student_id' => ['required', 'string', 'max:255', Rule::unique('user_profiles')->ignore($profileId)],
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|string|max:10',
            'bio' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
        ];
    }
}
