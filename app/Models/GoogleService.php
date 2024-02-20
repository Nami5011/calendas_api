<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Service_Calendar;
use Google_Oauth2Service;

class GoogleService extends Model
{
    use HasFactory;
	public function getClient() {
		// $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/v1/googleOauth';
		// Log::channel('stderr')->info('$redirect_uri');
		// Log::channel('stderr')->info($redirect_uri);

		$client = new Client();
		$client->setApplicationName("Calendas");
		$client->setAuthConfig(__DIR__.'/../google/client_secret_oauth.json');
		// $client->setRedirectUri($redirect_uri);
		$client->setScopes([Google_Service_Calendar::CALENDAR_EVENTS]);
		$client->setAccessType('offline');
		$client->setPrompt('consent');
		return $client;
	}

	public function getOauthService($client) {
		return new Google_Oauth2Service($client);
	}
}
