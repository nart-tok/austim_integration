<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Eventbrite;
use App\Http\Controllers\Controller;

class events extends Controller
{
  public function getEvent()
    {

    	$eventId = '65516643043';
        $event =  response()->json(Eventbrite::event()->get($eventId));
        

    }


}



