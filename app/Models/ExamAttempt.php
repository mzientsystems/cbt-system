
<?php

// app/Models/ExamAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'attempt_number',
        'start_time',
        'end_time',
        'duration_taken',
        'total_score',
        'percentage',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    public function isPassed()
    {
        return $this->percentage >= $this->exam->pass_mark;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'submitted' => 'Submitted',
            'timed_out' => 'Timed Out',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}