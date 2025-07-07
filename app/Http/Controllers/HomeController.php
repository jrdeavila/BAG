<?php

namespace App\Http\Controllers;

use App\Enums\ActivityStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $activities = User::find(Auth::user()->id)->activities()->where('status', ActivityStatus::PENDING)->paginate();
        return view('home', compact('activities'));
    }
}
