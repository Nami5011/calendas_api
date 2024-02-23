<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Oauth2;
use App\Helpers\DateTimeHelper;
use App\Helpers\LogHelper;

class GoogleService extends Model
{
    use HasFactory;
	public function getClient() {
		$client = new Client();
		$client->setApplicationName('Calendas');
		$client->setAuthConfig(__DIR__.'/../google/client_secret_oauth.json');
		$client->setScopes([
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/userinfo.profile',
			'https://www.googleapis.com/auth/calendar',
			'https://www.googleapis.com/auth/calendar.calendarlist',
			'https://www.googleapis.com/auth/calendar.settings.readonly',
			'https://www.googleapis.com/auth/calendar.freebusy',
		]);
		$client->setAccessType('offline');
		// $client->setPrompt('consent');
		return $client;
	}

	public function getOauthService($client) {
		return new Google_Service_Oauth2($client);
	}

	// Get user info *Require active token
	public function getUserinfoOauth($client) {
		$googleOauth = new Google_Service_Oauth2($client);
		$googleUserProfile = $googleOauth->userinfo->get();

		$userData = array();
		$userData['oauth_uid'] = !empty($googleUserProfile->id) ? $googleUserProfile->id : '';
		$userData['email']     = !empty($googleUserProfile->email) ? $googleUserProfile->email : '';
		$userData['locale']    = !empty($googleUserProfile->locale) ? $googleUserProfile->locale : '';
		$userData['picture']   = !empty($googleUserProfile->picture) ? $googleUserProfile->picture : '';
		return $userData;
	}

	public function getCalendarService($client) {
		return new Google_Service_Calendar($client);
	}

	public function getCalendarEvent($class) {
		$eventInput = array();
		$eventInput['summary'] = $class['summary'];
		if (isset($class['location'])) {
			$eventInput['location'] = $class['location'];
		}
		if (isset($class['description'])) {
			$eventInput['description'] = $class['description'];
		}
		$eventInput['start'] = array();
		$eventInput['start']['dateTime'] = DateTimeHelper::formatISO8601($class['startDateTime']);
		$eventInput['start']['timeZone'] = $class['timeZone'];
		$eventInput['end'] = array();
		$eventInput['end']['dateTime'] = DateTimeHelper::formatISO8601($class['endDateTime']);
		$eventInput['end']['timeZone'] = $class['timeZone'];
		if (isset($class['recurrenceList'])) {
			$eventInput['recurrence'] = $class['recurrenceList']; // array
		}
		if (isset($class['attendeeEmailList'])) {
			$attendees = array();
			foreach($class['attendeeEmailList'] as $email) {
				$attendees[] = array(
					'email' => $email,
				);
			}
			$eventInput['attendees'] = $attendees;
		}
		if (isset($class['reminders'])) {
			$eventInput['reminders'] = array();
			$eventInput['reminders']['useDefault'] = false;
			$overrides = array();
			foreach($class['reminders'] as $method => $minutes) {
				$overrides[] = array(
					'method'  => $method,
					'minutes' => $minutes,
				);
			}
			$eventInput['reminders']['overrides'] = $overrides;
		}
		return new Google_Service_Calendar_Event($eventInput);
	}

	public function checkCalendarEventRequest($class) {
		if (empty($class['summary'])
			|| empty($class['startDateTime'])
			|| empty($class['endDateTime'])
			|| empty($class['timeZone'])
		) {
			return false;
		}
		return true;
	}

}
