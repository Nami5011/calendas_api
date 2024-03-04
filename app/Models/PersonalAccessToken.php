<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;
	
	// service_name
	const SERVICE_NAME_GOOGLE = 'Google Calendar';

    protected $fillable = [
        'user_id',
		'oauth_uid',
        'service_name',
        'access_token',
		'refresh_token',
		'is_active',
    ];
	// public function personalAccessToken() {
	// 	return $this->belongsTo()
	// }

	public function scopeGoogleTokenByUserId($query, $id) {
		return $query->where('user_id', $id)
						->where('service_name', self::SERVICE_NAME_GOOGLE)
						->select('id', 'refresh_token', 'access_token', 'is_active');
	}
}
