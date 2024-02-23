<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\LogHelper;
use App\Models\GoogleService;
use App\Models\PersonalAccessToken;

class GoogleCalendarController extends Controller
{
    public function index()
    {

		// Log::channel('stderr')->info('stderr');
		// Log::channel('errorlog')->info('errorlog');
		LogHelper::Log('log');
		LogHelper::info('info');


        return 'success';
    }

	public function store(Request $request) {
        // Retrieve JSON data from the request body
        $reqData = $request->json()->all();
		// test
		$reqData['summary'] = 'test';
		$reqData['startDateTime'] = '2024-02-24 10:00:00';
		$reqData['endDateTime'] = '2024-02-24 11:00:00';
		$reqData['timeZone'] = 'Asia/Tokyo';
		$reqData['description'] = "改行\n確認<br>https://www.google.co.uk/";
		$reqData['location'] = 'https://www.google.co.uk/';
		$reqData['attendeeEmailList'] = ['maron.minzy@icloud.com'];

		if ($this->GoogleService()->checkCalendarEventRequest($reqData) === false) {
			return response()->json(['error' => 'Internal Server Error - Invalid request data'], 500);
		}

		// get refresh_token token
		$storedToken = PersonalAccessToken::where('user_id', 2)
										->where('service_name', PersonalAccessToken::SERVICE_NAME_GOOGLE)
										->where('is_active', true)
										->select('id', 'refresh_token', 'access_token')
										->first();
		
		if (!isset($storedToken)) {
			return response()->json(['error' => 'Internal Server Error - No access token'], 500);
		}
		
		// Oauth using Refresh Token
		$client = $this->GoogleService()->getClient();
		$token = $client->fetchAccessTokenWithRefreshToken($storedToken->refresh_token);
		if ($client->isAccessTokenExpired() && empty($token)) {
			LogHelper::info('Token Expired user_id: ');
			return response()->json(['error' => 'Internal Server Error - Access token expired'], 500);
		}

		// update access_token
		$storedToken->access_token = $token['access_token'];
		$storedToken->save();

		// Create google calendar event
		$service = $this->GoogleService()->getCalendarService($client);
		$event = $this->GoogleService()->getCalendarEvent($reqData);
		$calendarId = 'primary';
		$event = $service->events->insert($calendarId, $event);
		$eventLink = $event->htmlLink;
		return array(
			'htmlLink' => $eventLink,
		);
	}

	private function GoogleService() {
		if (!isset($this->GoogleService)) {
			$this->GoogleService = new GoogleService();
		}
		return $this->GoogleService;
	}
}
