<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogHelper
{
	public static function Log($param1, $param2=null)
	{
		if (!empty($param1) && !empty($param2)) {
			Log::channel('daily')->info($param1, [print_r($param2, true)]);
		} else if (!empty($param1)) {
			Log::channel('daily')->info(print_r($param1, true));
		}
	}

	public static function info($param1, $param2=null)
	{
		if (!empty($param1) && !empty($param2)) {
			Log::channel('errorlog')->info($param1, [$param2]);
		} else if (!empty($param1)) {
			Log::channel('errorlog')->info($param1);
		}
	}

}