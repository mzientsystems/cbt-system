
<?php

// app/Http/Controllers/Admin/StudentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Department;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\BulkImportStudentsRequest;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected $importService;

    public function __construct(StudentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index(Request $request)
    {
        $query = User::with(['profile.department'])
            ->whereHas('profile', function($q) {
                $q->whereNotNull('student_id');
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($pq) use ($search) {
                      $pq->where('student_id', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Department::where('status', 'active')->get();
        $levels = ['100', '200', '300', '400', '500'];

        return view('admin.students.index', compact('students', 'departments', 'levels'));
    }

    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        $levels = ['100', '200', '300', '400', '500'];
        
        return view('admin.students.create', compact('departments', 'levels'));
    }

    public function store(StoreStudentRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? Str::random(8)),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'status' => $request->status ?? 'active',
            ]);

            // Create profile
            UserProfile::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'department_id' => $request->department_id,
                'level' => $request->level,
                'bio' => $request->bio,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(User $student)
    {
        $student->load(['profile.department', 'examAttempts.exam']);
        
        return view('admin.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        $student->load('profile');
        $departments = Department::where('status', 'active')->get();
        $levels = ['100', '200', '300', '400', '500'];
        
        return view('admin.students.edit', compact('student', 'departments', 'levels'));
    }

    public function update(UpdateStudentRequest $request, User $student)
    {
        DB::beginTransaction();
        
        try {
            // Update user
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $student->update($userData);

            // Update profile
            $student->profile->update([
                'student_id' => $request->student_id,
                'department_id' => $request->department_id,
                'level' => $request->level,
                'bio' => $request->bio,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $student)
    {
        DB::beginTransaction();
        
        try {
            // Check if student has exam attempts
            if ($student->examAttempts()->count() > 0) {
                return back()->with('error', 'Cannot delete student with existing exam attempts.');
            }

            $student->profile()->delete();
            $student->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function bulkImport()
    {
        $departments = Department::where('status', 'active')->get();
        
        return view('admin.students.bulk-import', compact('departments'));
    }

    public function processBulkImport(BulkImportStudentsRequest $request)
    {
        try {
            $result = $this->importService->import(
                $request->file('csv_file'),
                $request->department_id,
                $request->level,
                $request->boolean('update_existing', false)
            );

            $message = "Import completed. {$result['imported']} students imported";
            if ($result['updated'] > 0) {
                $message .= ", {$result['updated']} updated";
            }
            if ($result['failed'] > 0) {
                $message .= ", {$result['failed']} failed";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ];

        $template = [
            ['student_id', 'first_name', 'last_name', 'email', 'username', 'phone', 'gender', 'date_of_birth', 'address'],
            ['STU001', 'John', 'Doe', 'john.doe@example.com', 'johndoe', '08012345678', 'male', '2000-01-15', '123 Example St'],
            ['STU002', 'Jane', 'Smith', 'jane.smith@example.com', 'janesmith', '08087654321', 'female', '1999-05-20', '456 Sample Ave'],
        ];

        $callback = function() use ($template) {
            $file = fopen('php://output', 'w');
            foreach ($template as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
