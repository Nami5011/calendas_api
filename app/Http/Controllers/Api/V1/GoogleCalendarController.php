<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\LogHelper;
use App\Helpers\GoogleServiceProcessBundle;
use App\Helpers\GoogleService;
use App\Models\Event;

class GoogleCalendarController extends Controller
{
    public function index()
    {

		// Log::channel('stderr')->info('stderr');
		// Log::channel('errorlog')->info('errorlog');
		LogHelper::Log('log');
		LogHelper::info('info');


        return ['success'];
    }

	public function store(Request $request) {
        // Retrieve JSON data from the request body
        $reqData = $request->json()->all();
		// test
		// $reqData['event_code'] = '4184c508-023a-41a4-aa03-aa17b7284344';
		// $reqData['summary'] = 'test';
		// $reqData['startDateTime'] = '2024-02-24 11:00:00';
		// $reqData['endDateTime'] = '2024-02-24 12:00:00';
		// $reqData['timeZone'] = 'Asia/Tokyo';
		// $reqData['description'] = "改行\n確認<br>https://www.google.co.uk/";
		// $reqData['location'] = 'https://www.google.co.uk/';
		// $reqData['attendeeEmailList'] = ['maron.011821@gmail.com'];

		if (GoogleService::checkCalendarEventRequest($reqData) === false) {
			return response()->json(['error' => 'Internal Server Error - Invalid request data'], 500);
		}

		// get the event
		$event = Event::activeEventByCode($reqData['event_code'])->first();
		if (!isset($event) || empty($event->user_id)) {
			return response()->json(['error' => 'Internal Server Error - No event found'], 500);
		}

		// Authenticate google client
		list($client, $storedToken) = GoogleServiceProcessBundle::refreshTokenProcess($event->user_id);
		if (is_array($client) && !empty($client['error'])) {
			return response()->json(['error' => $client['error']], 500);
		}

		// Create google calendar event
		$service = GoogleService::getCalendarService($client);
		$event = GoogleService::getCalendarEvent($reqData);
		$calendarId = 'primary';
		$event = $service->events->insert($calendarId, $event);
		$eventLink = $event->htmlLink;
		return array(
			'htmlLink' => $eventLink,
		);
	}

}
