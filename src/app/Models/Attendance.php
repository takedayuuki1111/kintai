<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date'       => 'date',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeToday($query)
    {
        return $query->onDate(Carbon::today());
    }

    public function getTotalRestAttribute()
    {
        $total_rest_seconds = 0;

        foreach ($this->rests as $rest) {
            if ($rest->start_time && $rest->end_time) {
                $start = Carbon::parse($rest->start_time);
                $end = Carbon::parse($rest->end_time);
                $total_rest_seconds += $start->diffInSeconds($end);
            }
        }

        return $this->formatTime($total_rest_seconds);
    }

    public function getTotalWorkAttribute()
    {
        if (!$this->end_time) {
            return ''; 
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        $total_seconds = $start->diffInSeconds($end);


        $rest_seconds = 0;
        foreach ($this->rests as $rest) {
            if ($rest->start_time && $rest->end_time) {
                $restStart = Carbon::parse($rest->start_time);
                $restEnd = Carbon::parse($rest->end_time);
                $rest_seconds += $restStart->diffInSeconds($restEnd);
            }
        }

        $actual_work_seconds = $total_seconds - $rest_seconds;

        return $this->formatTime($actual_work_seconds);
    }

    private function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}