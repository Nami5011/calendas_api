<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Helpers\LogHelper;

class EventController extends Controller
{
    //
	public function getEvent()
	{
		LogHelper::info('code', $_GET['code']);
		if (empty($_GET['code'])) {
			return response()->json(['error' => 'No Code'], 500);
		}
		$event = Event::activeEventByCode($_GET['code'])->first();

		return $event;
	}
}
