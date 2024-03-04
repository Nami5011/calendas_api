<?php

namespace App\Helpers;

use App\Helpers\LogHelper;
use App\Models\PersonalAccessToken;
use App\Helpers\GoogleService;

class GoogleServiceProcessBundle
{
	// Ritreave refresh_token by user_id, check access token, return client and token
	public static function refreshTokenProcess($user_id) {
		// get refresh_token token
		$storedToken = PersonalAccessToken::googleTokenByUserId($user_id)->where('is_active', true)->first();
		if (!isset($storedToken)) {
			return ['error' => 'Internal Server Error - No access token'];
		}

		// Oauth using Refresh Token
		$client = GoogleService::getClient();
		$token = $client->fetchAccessTokenWithRefreshToken($storedToken->refresh_token);
		if ($client->isAccessTokenExpired() && empty($token)) {
			LogHelper::info('Token Expired user_id: ');
			return ['error' => 'Internal Server Error - Access token expired'];
		}

		// update access_token
		$storedToken->access_token = $token['access_token'];
		$storedToken->save();

		return [$client, $storedToken];
	}

}
