<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function __construct() {

    }

    public function index(Request $request, $id, $eventid) {
        $event = \App\Event::with('attendees')->where('id', $id)->where('event_id', $eventid)->first();
        $data['event'] = $event;
        if ($event)
            return view('form.index', $data);
        else 
            abort(404);
    }

    public function add(Request $request, $eventid) {
        $event = \App\Event::with('user')->where('event_id', $eventid)->first();
        if( !$event )
            abort(404);
        else {
            if( $event->active == 0 )
                return redirect()->back();
            $request->validate([
                'email' => 'required|email:rfc,dns|max:50',
                'g-recaptcha-response' => 'required|recaptchav3:join,0.5'
            ]);

            $client = $this->getClient($event);
            $service = new \Google_Service_Calendar($client);

            $calendarId = 'primary';

            $gEvent = $service->events->get($calendarId, $eventid);
            $attendees = $gEvent->getAttendees();
            $additional = new \Google_Service_Calendar_EventAttendee(['email' => $request->email]);
            array_push($attendees, $additional);
            $gEvent->setAttendees($attendees);
            $gEventUpdate = $service->events->patch($calendarId, $eventid, $gEvent, ['conferenceDataVersion' => 1, 'sendNotifications' => true]);

            if( $gEventUpdate ) {
                $att = \App\Attendee::insert([
                    'id' => Str::uuid(),
                    'email' => $request->email,
                    'event_id' => $event->id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
                // $att->save();
                return redirect(route('form', ['id' => $event->id, 'eventid' => $eventid]));
            }
        }
    }

    function getClient($event){
        $userToken = $event->user->token;
        $client = new \Google_Client();
        $client->setScopes(\Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(config('services.google.credentials_file'));
        $client->setRedirectUri(config('services.google.redirect_additional'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        if ($userToken) {
            $accessToken = json_decode($event->user->token, true);
            $client->setAccessToken($accessToken);
        }
 
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Exchange authorization code for an access token.
                // if ( isset($_GET['code']) ){
                //     $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                //     // file_put_contents($tokenPath, json_encode($accessToken));
                //     $client->setAccessToken($accessToken);
                //     redirect(route('events.add'));
                // } 

                // if( !$request->user()->token) {
                //     echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
                // } else {
                //     redirect(route('events.add'));
                // }
                abort(503);
                
            }
        }
        return $client;
    }
}
