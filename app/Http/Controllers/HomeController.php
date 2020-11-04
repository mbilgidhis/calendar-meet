<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $events = \App\Event::where('user_id', $request->user()->id)->where('created_at', '>=', \Carbon\Carbon::now()->subDays(60))->get();
        $evs = array();
        foreach ($events as $event) {
            $temp = array();
            $temp['startDate'] = $event->start_at;
            $temp['endDate'] = $event->end_at;
            $temp['summary'] = $event->name;
            array_push($evs, $temp);
        }

        $data['events'] = $evs;
        return view('home', $data);
    }
}
