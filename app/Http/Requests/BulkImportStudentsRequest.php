
<?php

// app/Http/Requests/BulkImportStudentsRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkImportStudentsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Add proper authorization logic
    }

    public function rules()
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|string|max:10',
            'update_existing' => 'boolean',
        ];
    }
}
