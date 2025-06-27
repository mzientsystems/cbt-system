
<?php

// app/Services/StudentImportService.php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentImportService
{
    public function import(UploadedFile $file, $departmentId, $level, $updateExisting = false)
    {
        $csvData = $this->parseCsv($file);
        $results = [
            'imported' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header
                
                try {
                    $validated = $this->validateRow($row, $rowNumber);
                    
                    if ($validated['valid']) {
                        $result = $this->processStudent($validated['data'], $departmentId, $level, $updateExisting);
                        $results[$result]++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Row {$rowNumber}: " . implode(', ', $validated['errors']);
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();
            return $results;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function parseCsv(UploadedFile $file)
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) { // Minimum required fields
                $csvData[] = array_combine($header, $row);
            }
        }
        
        fclose($handle);
        return $csvData;
    }

    private function validateRow($row, $rowNumber)
    {
        $rules = [
            'student_id' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string'
        ];

        $validator = Validator::make($row, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all()
            ];
        }

        // Additional unique checks
        $errors = [];
        
        // Check if student_id already exists
        if (UserProfile::where('student_id', $row['student_id'])->exists()) {
            $errors[] = "Student ID {$row['student_id']} already exists";
        }

        // Check if email already exists
        if (User::where('email', $row['email'])->exists()) {
            $errors[] = "Email {$row['email']} already exists";
        }

        // Check if username already exists
        if (User::where('username', $row['username'])->exists()) {
            $errors[] = "Username {$row['username']} already exists";
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        return [
            'valid' => true,
            'data' => $row
        ];
    }

    private function processStudent($data, $departmentId, $level, $updateExisting)
    {
        // Check if student exists
        $existingProfile = UserProfile::where('student_id', $data['student_id'])->first();
        
        if ($existingProfile && !$updateExisting) {
            throw new \Exception("Student with ID {$data['student_id']} already exists");
        }

        if ($existingProfile && $updateExisting) {
            // Update existing student
            $user = $existingProfile->user;
            
            $user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'phone' => $data['phone'] ?? null,
            ]);

            $existingProfile->update([
                'department_id' => $departmentId,
                'level' => $level,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            return 'updated';
        }

        // Create new student
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(8)), // Generate random password
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'student_id' => $data['student_id'],
            'department_id' => $departmentId,
            'level' => $level,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return 'imported';
    }
}
