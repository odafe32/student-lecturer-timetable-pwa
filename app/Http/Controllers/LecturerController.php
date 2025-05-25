<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PushNotificationController;

class LecturerController extends Controller
{
    /**
     * Show the lecturer dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        return view('lecturer.dashboard');
    }


}