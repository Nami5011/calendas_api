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
}
