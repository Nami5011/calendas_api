<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Helpers\GoogleServiceProcessBundle;
use App\Helpers\LogHelper;
use App\Helpers\GoogleService;

class UserController extends Controller
{
	public function index()
	{
		$encodedEmail = isset($_GET['key']) ? $_GET['key'] : '';
		$email = base64_decode(urldecode($encodedEmail));
		$storedUser = User::byEmail($email)->first();

		LogHelper::info('UserController $storedUser', $storedUser);
		if (!$storedUser) {
			return [];
		}
		$result            = array();
		$result['user_id'] = $storedUser->id;
		$result['email']   = $email;

		// Authenticate google client
		list($client, $storedToken) = GoogleServiceProcessBundle::refreshTokenProcess($storedUser->id);
		if (is_array($client) && !empty($client['error'])) {
			return response()->json(['error' => $client['error']], 500);
		}

		// Getting user profile info 
		$googleUserProfile = GoogleService::getUserinfoOauth($client);
		
		$result['locale']   = $googleUserProfile['locale'];
		$result['picture']  = $googleUserProfile['picture'];
		$result['token_id'] = $storedToken->id;
		return $result;
	}

}
