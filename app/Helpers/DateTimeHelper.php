<?php
namespace App\Helpers;

use DateTime;

class DateTimeHelper
{
	public static function formatISO8601($dateTimeString = null)
	{
		$date = isset($dateTimeString) ? new DateTime($dateTimeString) : new DateTime();
		$formattedDate = $date->format(DateTime::ATOM);
		return $formattedDate;
	}

}