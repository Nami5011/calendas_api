<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleService;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Helpers\LogHelper;

class GoogleOauthController extends Controller
{
    public function index(Request $request)
    {
		// return ['result' => 'ok'];
		Log::channel('stderr')->info('OAUTH IS CALLED');
        $client = $this->GoogleService()->getClient();

		// Google Oauth URL
		$result = [
			'url' => $client->createAuthUrl(),
		];
		return $result;
    }

    public function store(Request $request)
    {
		$authCode = $request->json('code');

		if (empty($authCode)) {
			// Error
			return response()->json(['error' => 'Internal Server Error - No auth code'], 500);
		}
		$client = $this->GoogleService()->getClient();
		$token = $client->fetchAccessTokenWithAuthCode($authCode);
		// $client->authenticate($authCode);
		// $token = $client->getAccessToken();
		if (isset($token['access_token'])) {
			$client->setAccessToken($token['access_token']);
		}
		LogHelper::Log('$token');
		LogHelper::Log($token);

		if (empty($client->getAccessToken())) {
			Log::channel('daily')->info('failed getAccessToken');
			// Error
			return response()->json(['error' => 'Internal Server Error - No access token'], 500);
		}

		// Get user profile data from google
		$googleUserProfile = $this->GoogleService()->getUserinfoOauth($client);
		LogHelper::Log('google_user_profile', $googleUserProfile);
		
		// Get User 
		$storedUser = $this->getOrCreateUser($googleUserProfile);
		if (!isset($storedUser)) {
			// Error
			return response()->json(['error' => 'Internal Server Error - Failed get/create user'], 500);
		}
		// Update Token
		$storedToken = $this->updateOrCreateToken($storedUser->id, $googleUserProfile, $token);
		if (!isset($storedToken)) {
			// Error
			return response()->json(['error' => 'Internal Server Error - Failed update/create token'], 500);
		}

		$result             = array();
		$result['email']    = $googleUserProfile['email'];
		$result['locale']   = $googleUserProfile['locale'];
		$result['picture']  = $googleUserProfile['picture'];
		$result['user_id']  = $storedUser->id;
		$result['token_id'] = $storedToken->id;
		return $result;
	}

	private function getOrCreateUser($userData) {
		$user = User::where('email', $userData['email'])->select('id', 'name', 'email')->first();
		if (!isset($user)) {
			$user = User::create([
				'email' => $userData['email'],
			]);
		}
		return $user;
	}

	private function updateOrCreateToken($id, $userData, $token) {
		// Save Token
		$updatedToken = PersonalAccessToken::updateOrCreate(
			[
				// update target columns
				'refresh_token' => $token['refresh_token'],
				'access_token'  => $token['access_token'],
				'is_active'     => true,
			],[
				// matching arguments
				'user_id'      => $id,
				'oauth_uid'    => $userData['oauth_uid'],
				'service_name' => PersonalAccessToken::SERVICE_NAME_GOOGLE,
			],
		);
		return $updatedToken;
	}

	private function GoogleService() {
		if (!isset($this->GoogleService)) {
			$this->GoogleService = new GoogleService();
		}
		return $this->GoogleService;
	}

}
