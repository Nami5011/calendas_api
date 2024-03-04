<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'event_code',
        'description',
        'confirmation_message',
        'available_datetime',
        'start_day_length',
        'count',
        'is_active',
        'send_email_flg',
    ];

    protected $casts = [
        'available_datetime' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        // Set a unique value when creating a new event
        static::creating(function ($event) {
            $event->event_code = Str::uuid();
        });
    }

	public function scopeActiveEventByCode($query, $eventCode) {
		return $query->where('event_code', $eventCode)
						->where('is_active', true)
						->select('user_id',
						'title',
						'description',
						'confirmation_message',
						'available_datetime',
						'start_day_length',
						'count',
						'send_email_flg');
	}
}
