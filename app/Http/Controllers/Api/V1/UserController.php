<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\PersonalAccessToken;
use App\Models\GoogleService;
use App\Helpers\LogHelper;

class UserController extends Controller
{
	public function index()
	{
		$encodedEmail = isset($_GET['key']) ? $_GET['key'] : '';
		$email = base64_decode(urldecode($encodedEmail));
		$storedUser = User::where('email', $email)->select('id')->first();

		LogHelper::info('UserController $storedUser', $storedUser);
		if (!$storedUser) {
			return [];
		}
		$result            = array();
		$result['user_id'] = $storedUser->id;
		$result['email']   = $email;

		$storedToken = PersonalAccessToken::where('user_id', $storedUser->id)
										->where('service_name', PersonalAccessToken::SERVICE_NAME_GOOGLE)
										->select('id', 'refresh_token', 'access_token', 'is_active')
										->first();
		if (!isset($storedToken)
			|| (isset($storedToken) && !$storedToken->is_active)
		) {
			return $result;
		}

		// Google Token Check
		$client = $this->GoogleService()->getClient();

		// Oauth using Refresh Token
		$token = $client->fetchAccessTokenWithRefreshToken($storedToken->refresh_token);
		if ($client->isAccessTokenExpired() && empty($token)) {
			LogHelper::info('Token Expired user_id: ' . $result['user_id']);
			return $result;
		}

		// update access_token
		$storedToken->access_token = $token['access_token'];
		$storedToken->save();

		// Getting user profile info 
		$googleUserProfile = $this->GoogleService()->getUserinfoOauth($client);
		
		$result['locale']   = $googleUserProfile['locale'];
		$result['picture']  = $googleUserProfile['picture'];
		$result['token_id'] = $storedToken->id;
		return $result;
	}

	private function GoogleService() {
		if (!isset($this->GoogleService)) {
			$this->GoogleService = new GoogleService();
		}
		return $this->GoogleService;
	}
}
