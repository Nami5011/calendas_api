<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleService;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    public function index()
    {
        return 'success';
    }

}
