<?php

// app/Models/User.php (Enhanced)
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'status',
        'role_id',
        'email_verified_at',
        'last_login_at',
        'password_changed_at',
        'force_password_change'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'locked_until' => 'datetime',
        'force_password_change' => 'boolean'
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function createdQuestionBanks()
    {
        return $this->hasMany(QuestionBank::class, 'created_by');
    }

    public function createdQuestions()
    {
        return $this->hasMany(Question::class, 'created_by');
    }

    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasRole($role)
    {
        return $this->role->name === $role;
    }

    public function hasPermission($permission)
    {
        return $this->role->hasPermission($permission);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isLecturer()
    {
        return $this->hasRole('lecturer');
    }

    public function isStudent()
    {
        return $this->hasRole('student');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_until' => Carbon::now()->addMinutes($minutes),
            'login_attempts' => 0
        ]);
    }

    public function incrementLoginAttempts()
    {
        $this->increment('login_attempts');
        
        if ($this->login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    public function resetLoginAttempts()
    {
        $this->update(['login_attempts' => 0]);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => Carbon::now()]);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
        $this->attributes['password_changed_at'] = Carbon::now();
    }

    public function needsPasswordChange()
    {
        return $this->force_password_change || 
               ($this->password_changed_at && $this->password_changed_at->diffInDays() > 90);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->whereHas('role', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    public function scopeStudents($query)
    {
        return $query->byRole('student');
    }

    public function scopeLecturers($query)
    {
        return $query->byRole('lecturer');
    }

    public function scopeAdmins($query)
    {
        return $query->byRole('admin');
    }
}

public function isAdmin()
{
    // Implement your admin check logic
    // This could be role-based or checking specific fields
    return $this->email === 'admin@system.com' || $this->username === 'admin';
}