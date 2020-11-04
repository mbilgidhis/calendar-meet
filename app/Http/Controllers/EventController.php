<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar_Event;
use Google_Service_Calendar;
use Google_Service_Oauth2;
use App\User;
use App\Event;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct() {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(Request $request) {
        $data['events'] = Event::with('user')->orderBy('start_at', 'asc')->paginate(10);
        return view('events/index', $data);
    }

    public function add(Request $request) {
        return view('events/add');
    }

    public function save(Request $request) {
        return $request->all();
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'start' => 'required|date|date_format:Y-m-d H:i:s',
            'end' => 'required|date|date_format:Y-m-d H:i:s|after:start_at',
            'g-recaptcha-response' => 'required|recaptchav3:add,0.5'
        ]);
        $event = new Event();
        $event->id = Str::uuid();
        $event->name = $request->name;
        $event->description = $request->description;
        $event->start_at = $request->start;
        $event->end_at = $request->end;
        $event->user_id = $request->user()->id;

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $request->start);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $request->end);


        $gEvent = new \Google_Service_Calendar_Event(
            array(
                'summary' => $request->name,
                'description' => $request->description,
                'start' => array(
                    'dateTime' => $start->format('Y-m-d\TH:i:sP'),
                    'timeZone' => 'Asia/Jakarta'
                ),
                'end' => array(
                    'dateTime' => $end->format('Y-m-d\TH:i:sP'),
                    'timeZone' => 'Asia/Jakarta'
                )
            )
        );

        $client = $this->getClient($request);
        $service = new \Google_Service_Calendar($client);

        $calendarId = 'primary';

        $gEvent = $service->events->insert($calendarId, $gEvent);

        if( $gEvent ) {
            $conference = new \Google_Service_Calendar_ConferenceData();
            $conferenceRequest = new \Google_Service_Calendar_CreateConferenceRequest();
            $conferenceRequest->setRequestId(Str::random(10));
            $conference->setCreateRequest($conferenceRequest);
            $gEvent->setConferenceData($conference);
            $gEvent = $service->events->patch($calendarId, $gEvent->id, $gEvent, ['conferenceDataVersion' => 1]);
            $event->event_id = $gEvent->id;
            $event->event_link = $gEvent->htmlLink;
            $event->meet_link = $gEvent->hangoutLink;
            $event->save();
            return redirect(route('events.list'));
        }
    }

    public function edit(Request $request, $id) {
        $event = Event::with('user')->where('id', $id)->where('user_id', $request->user()->id)->first();
        $data['event'] = $event;
        if( $event )
            return view('events/edit', $data);
        else
            abort(404);
    }

    public function update( Request $request ) {
        // return $request->all();
        $request->validate([
            'id' => 'required|string|uuid',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'start' => 'required|date|date_format:Y-m-d H:i:s',
            'end' => 'required|date|date_format:Y-m-d H:i:s|after:start',
            'g-recaptcha-response' => 'required|recaptchav3:update,0.5'
        ]);

        $event = Event::where('id', $request->id)->where('user_id', $request->user()->id)->first();

        $event->name = $request->name;
        $event->description = $request->description;
        $event->start_at = $request->start;
        $event->end_at = $request->end;
        $event->active = ( $request->active == 'on' ) ? 1 : 0;

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $request->start);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $request->end);

        $gEvent = new \Google_Service_Calendar_Event(
            array(
                'summary' => $request->name,
                'description' => $request->description,
                'start' => array(
                    'dateTime' => $start->format('Y-m-d\TH:i:sP'),
                    'timeZone' => 'Asia/Jakarta'
                ),
                'end' => array(
                    'dateTime' => $end->format('Y-m-d\TH:i:sP'),
                    'timeZone' => 'Asia/Jakarta'
                )
            )
        );

        $client = $this->getClient($request);
        $service = new \Google_Service_Calendar($client);

        $calendarId = 'primary';

        $gEvent = $service->events->patch($calendarId, $event->event_id, $gEvent);
        if( $gEvent ) {
            $event->save();
            return redirect(route('events.list'));
        }
    }

    public function getToken(Request $request) {
        $client = new Google_Client();
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(config('services.google.credentials_file'));
        $client->setRedirectUri(config('services.google.redirect_additional'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        if ($request->user()->token) {
            $accessToken = json_decode($request->user()->token, true);
            $client->setAccessToken($accessToken);
        }
 
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {

                // Exchange authorization code for an access token.
                if ( isset($_GET['code']) ){
                    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                    $user = User::where('id', $request->user()->id)->first();
                    $user->token = json_encode($accessToken);
                    $user->save();
                    // file_put_contents($tokenPath, json_encode($accessToken));
                    $client->setAccessToken($accessToken);
                    return redirect(route('events.add'));
                } 
                
                if( !$request->user()->token) {
                    echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
                } else {
                    return redirect(route('events.add'));
                }
                
            }
        }
    }

    function getClient($request){
        $client = new Google_Client();
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(config('services.google.credentials_file'));
        $client->setRedirectUri(config('services.google.redirect_additional'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        if ($request->user()->token) {
            $accessToken = json_decode($request->user()->token, true);
            $client->setAccessToken($accessToken);
        }
 
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {

                // Exchange authorization code for an access token.
                if ( isset($_GET['code']) ){
                    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                    $user = User::where('id', $request->user()->id)->first();
                    $user->token = json_encode($accessToken);
                    $user->save();
                    // file_put_contents($tokenPath, json_encode($accessToken));
                    $client->setAccessToken($accessToken);
                    redirect(route('events.add'));
                } 

                if( !$request->user()->token) {
                    echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
                } else {
                    redirect(route('events.add'));
                }
                
            }
        }
        return $client;
    }
}
