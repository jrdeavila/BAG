<?php

namespace App\Http\Controllers;

use App\Enums\ActivityPriority;
use App\Enums\ActivityStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Hr;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $activities = User::find(Auth::user()->id)->activities()->where('status', ActivityStatus::PENDING)
            ->orderBy('priority', 'desc')
            ->paginate(5);
        return view('home', compact('activities'));
    }
}
