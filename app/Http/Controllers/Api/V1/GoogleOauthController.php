<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleService;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class GoogleOauthController extends Controller
{
    public function index(Request $request)
    {
		// return ['result' => 'ok'];
		Log::channel('stderr')->info('OAUTH IS CALLED');
		Log::channel('stderr')->info('code');
		Log::channel('stderr')->info($_GET['code']);
        $client = $this->GoogleService()->getClient();

		$access_token = null;
        if (isset($_GET['code'])) {
			Log::channel('stderr')->info('OAUTH IS CALLED After call back');
			// After call back
			$this->oauthCallBackProcess($client, $_GET['code']);
			return;
		}
		// Google Oauth URL
		$result = [
			'url' => $client->createAuthUrl(),
		];

		// Oauth using Refresh Token
		// $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);

		// if ($client->isAccessTokenExpired() && empty($token)) {
		// 	return ['success' => false, 'message' => ['認証に失敗しました。']];
		// }
		return $result;
    }

	private function oauthCallBackProcess($client, $code) {
		$google_oauth = $this->GoogleService()->getOauthService($client);
		$client->authenticate($code);
		$token = $client->fetchAccessTokenWithAuthCode($code);
		Log::channel('stderr')->info('token', $token);
		if (isset($token['access_token'])) {
			$client->setAccessToken($token['access_token']);
		}
		if ($client->getAccessToken()) {
			// Get user profile data from google
			$google_user_profile = $google_oauth->userinfo->get();
		}
		// Save Refresh Token
		PersonalAccessToken::create([
			'user_id' => 1,
			'name'    => 'Google Calendar',
			'token'   => $data['refresh_token'],
		]);
	}

	private function GoogleService() {
		if (!isset($this->GoogleService)) {
			$this->GoogleService = new GoogleService();
		}
		return $this->GoogleService;
	}

}
