<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Client;
use App\Course;
//use Eventbrite;
//use App\Http\Controllers\Controller;

class events extends Controller
{
  public function getEvent()
    {
    	// ### Adding webhook event data to the database  ###
    	//Getting event id from the webhook
    	$array_webook_last = [];
		$event_webhook_json = file_get_contents('http://ambasd.tw1.ru/eventbrite.txt');
		$array_webhook = json_decode($event_webhook_json,true);
		$array_webook_last= explode('/', $array_webhook['data']['api_url']) ;
		$eventId = $array_webook_last[5];
		$search = $eventId;
   		//echo 'The event id is : ' . $eventId;
   		//checking if that event ID exists already or not 
   		$events = event::all();
   		$events = json_decode($events, TRUE);
   		foreach ($events as $event) {
   			if($event['event_id'] == $eventId ){
   				echo 'MATCH FOUND // No New events';
   				exit();
   			}
   		}

     	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/". $eventId ."/?token=HVI4TKUB4HG4AA25SWQN");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response,true);
		//print("<pre>".print_r($result,true)."</pre>");
		$name = $result['name']['text'];
		$description = $result['description']['text'];
		$link = $result['url'];
		$picture_link = $result['logo']['original']['url'];
		// Getting the exact date - time 
		$start = $result['start']['local']; $timestamp = strtotime($start); $start_day = date('D', $timestamp);
		$timezone = $result['start']['timezone'];
		$start_time = date('h:ia', $timestamp);
		$year = date('Y', $timestamp);
		$end = $result['end']['local']; $timestamp = strtotime($end); $end_day = date('D', $timestamp);
		$end_time = date('h:ia', $timestamp);
		$event_time = 'Days : '.$start_day .' - '.$end_day .' ' . $start_time .' - ' . $end_time;
		//echo $event_time;
		$venue_id = $result['venue_id'];

		// Getting the event Address
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/venues/".$venue_id."/?token=HVI4TKUB4HG4AA25SWQN");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response,true);
		//print("<pre>".print_r($result,true)."</pre>");
		$address = $result['name'] . ',' .$result['address']['localized_address_display'];
		//echo $address;
		
		// Getting the event price
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/".$eventId ."/ticket_classes/?token=HVI4TKUB4HG4AA25SWQN");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		$results = json_decode($response,true);
		//print("<pre>".print_r($results,true)."</pre>");

		foreach ($results['ticket_classes'] as $result) {
			echo ' <br/>' . $result['name'];
			if ($result['name'] == 'Autism Fitness Level 1 Early Enrollment' || $result['name'] == 'Autism Fitness Certification L1 Early Enrollment'){
				$early = $result['actual_cost']['display'];
			}
			if ($result['name'] == 'Autism Fitness Level 1 Enrollment' || $result['name'] == 'Autism Fitness L1 Enrollment'){
				$actual = $result['actual_cost']['display'];
				$actual_start_date = $result['sales_start'];
			}
		}
		//echo '<br/> actual price ' . $actual;
		//echo '<br/> Sale price ' .$early;
		//echo '<br/> sale ends ' . $actual_start_date;
		$actual_start_date = strtotime($actual_start_date);
		//echo $actual_start_date;
		//echo '<br/> event name is : <h2>' . $name . '</h2>';
		//echo ' url is : ' . $picture_link;
		
		//inserting data to DB
		$event = new Event();
          $event->title = $name;
          $event->description = $description;
          $event->event_id = $eventId;
          $event->date = $start;
          $event->event_time = $event_time;
          $event->address = $address;
          $event->price = $early;
          $event->final_price = $actual;
          $event->enroll_date = $actual_start_date;
          $event->picture_link = $picture_link;
          $event->year = $year;
          $event->publish = 1;
          $event->event_url = $link;
          $event->sale_ends = $actual_start_date;
          $event->time_zone = $timezone;
          $event->eventbrite = 1;
          $event->lms_key = 'empty';
          $event->lms_group_id = 'empty';
          $event->l1_course_added = 0;
          $event->test = 0;

		$event->save();
		echo 'new event ' . $eventId;

    }

    public function updateEvent()
    {
    	// ### Adding webhook event data to the database  ###
    	//Getting event id from the webhook
    	$array_webook_last = [];
		$event_webhook_json = file_get_contents('http://ambasd.tw1.ru/eventbrite_updated.txt');
		$array_webhook = json_decode($event_webhook_json,true);
		$array_webook_last= explode('/', $array_webhook['data']['api_url']) ;
		$eventId = $array_webook_last[5];
		$search = $eventId;
   		//echo 'The event id is : ' . $eventId;
   		//checking if that event ID exists already or not 
   		$events = event::all();
   		$events = json_decode($events, TRUE);
   		foreach ($events as $event) {
   			if($event['event_id'] == $eventId ){
   				echo 'MATCH FOUND // Updating event : <br/>';
   				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/". $eventId ."/?token=HVI4TKUB4HG4AA25SWQN");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				$response = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($response,true);
				//print("<pre>".print_r($result,true)."</pre>");
				$name = $result['name']['text'];
				$description = $result['description']['text'];
				$link = $result['url'];
				$picture_link = $result['logo']['original']['url'];
				// Getting the exact date - time 
				$start = $result['start']['local']; $timestamp = strtotime($start); $start_day = date('D', $timestamp);
				$timezone = $result['start']['timezone'];
				$start_time = date('h:ia', $timestamp);
				$year = date('Y', $timestamp);
				$end = $result['end']['local']; $timestamp = strtotime($end); $end_day = date('D', $timestamp);
				$end_time = date('h:ia', $timestamp);
				$event_time = 'Days : '.$start_day .' - '.$end_day .' ' . $start_time .' - ' . $end_time;
				//echo $event_time;
				$venue_id = $result['venue_id'];

				// Getting the event Address
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/venues/".$venue_id."/?token=HVI4TKUB4HG4AA25SWQN");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				$response = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($response,true);
				//print("<pre>".print_r($result,true)."</pre>");
				$address = $result['name'] . ',' .$result['address']['localized_address_display'];
				//echo $address;
				
				// Getting the event price
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/".$eventId ."/ticket_classes/?token=HVI4TKUB4HG4AA25SWQN");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				$response = curl_exec($ch);
				curl_close($ch);
				$results = json_decode($response,true);
				//print("<pre>".print_r($results,true)."</pre>");

				foreach ($results['ticket_classes'] as $result) {
					echo ' <br/>' . $result['name'];
					if ($result['name'] == 'Autism Fitness Level 1 Early Enrollment' || $result['name'] == 'Autism Fitness Certification L1 Early Enrollment'){
						$early = $result['actual_cost']['display'];
					}
					if ($result['name'] == 'Autism Fitness Level 1 Enrollment' || $result['name'] == 'Autism Fitness L1 Enrollment'){
						$actual = $result['actual_cost']['display'];
						$actual_start_date = $result['sales_start'];
					}
				}
				echo '<br/> actual price ' . $actual;
				//echo '<br/> Sale price ' .$early;
				//echo '<br/> sale ends ' . $actual_start_date;
				$actual_start_date = strtotime($actual_start_date);
				//echo $actual_start_date;
				//echo '<br/> event name is : <h2>' . $name . '</h2>';
				echo ' url is : ' . $picture_link;
				
				//Updating  data in DB
				event::where('event_id' , $eventId)->update(
					['title' => $name,
					 'description' => $description,
					 'date' => $start,
					 'event_time' => $event_time,
					 'address' => $address,
					 'price' => $early,
					 'final_price' => $actual,
					 'enroll_date' => $actual_start_date,
					 'picture_link' => $picture_link,
					 'year' => $year,
					 'publish' => 1,
					 'event_url' => $link,
					 'sale_ends' => $actual_start_date,
					 'time_zone' => $timezone,
					 'eventbrite' => 1
					]);
				
				echo 'event updated ' . $eventId;
   				
   			}
   		}  	

    }

  public function showEvents()
    {
    	// ### fetches all the published event data from the database to be used later on website ###
    	$events = event::all()->where('publish','1')->where('test','0')->sortBy('date');
    	echo $events;
    	

    }

  public function mailchimp()
    {


    	$events = event::all()->where('publish','1')->where('test','0')->sortBy('date');
    	$date = new \DateTime();    // Todays date 
		$date->modify('-43 days'); // 6 weeks and 1 day from now (when the exams are closed)
		echo $date->format('Y-m-d') . '<br/>';
		$max_date = strtotime($date->format('Y-m-d'));
		echo $max_date . '<br/><br/>';

    	//echo count($events);
    	foreach ($events as $event) {
    		if ($max_date <  strtotime($event['date'])){ // event is still active we proceed
    			echo $event['date'] . '<br/>';
    			$current_date = new \DateTime();
    			$current_date = strtotime($current_date->format('Y-m-d'));
    			/*******************************************************************************/    			
    			$thirty_days_before_event = new \DateTime($event['date']);
    			$thirty_days_before_event->modify('-30 days');
    			$thirty_days_before_event = strtotime($thirty_days_before_event->format('Y-m-d')); 
    			if($current_date >=  $thirty_days_before_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "30 Days Before Event" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '30 days before done';
    			     }
    			    }
    			}
    			/*******************************************************************************/			   			
    			$fourteen_days_before_event = new \DateTime($event['date']);
    			$fourteen_days_before_event->modify('-14 days');
    			$fourteen_days_before_event = strtotime($fourteen_days_before_event->format('Y-m-d')); 
    			 if($current_date ==  $fourteen_days_before_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "14 Days Before Event" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '14 days before done';
    			     }
    			    }
    			}

    			/*******************************************************************************/ 			   			
    			$seven_days_before_event = new \DateTime($event['date']);
    			$seven_days_before_event->modify('-7 days');
    			$seven_days_before_event = strtotime($seven_days_before_event->format('Y-m-d')); 
    			 if($current_date ==  $seven_days_before_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "7 Days Before Event" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '7 days before done';
    			     }
    			    }
    			}

    			/*******************************************************************************/ 			   			
    			$eighteen_hours_before_event = new \DateTime($event['date']);
    			$eighteen_hours_before_event->modify('-18 hours');
    			$eighteen_hours_before_event = strtotime($eighteen_hours_before_event->format('Y-m-d')); 
    			 if($current_date ==  $eighteen_hours_before_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "18 Hours Before Event" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '18 hours before done';
    			     }
    			    }
    			}

    			/*******************************************************************************/ 
    			$three_days_after_event = new \DateTime($event['date']);
    			$three_days_after_event->modify('+3 days');
    			$three_days_after_event = strtotime($three_days_after_event->format('Y-m-d')); 
    			 if($current_date == $three_days_after_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "3 Days After Live Event" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '3 days after done';
    			     }
    			    }
    			}
    			/*******************************************************************************/
    			$three_weeks_after_event = new \DateTime($event['date']);
    			$three_weeks_after_event->modify('+3 weeks');
    			$three_weeks_after_event = strtotime($three_weeks_after_event->format('Y-m-d')); 
    			 if($current_date == $three_weeks_after_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "3 weeks after exams start" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '3 weeks after done';
    			     }
    			    }
    			}
    			/*******************************************************************************/
    			$five_weeks_after_event = new \DateTime($event['date']);
    			$five_weeks_after_event->modify('+5 weeks');
    			$five_weeks_after_event = strtotime($five_weeks_after_event->format('Y-m-d')); 
    			 if($current_date == $five_weeks_after_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "5 Weeks after exam" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '5 weeks after done';
    			     }
    			    }
    			}
    			/*******************************************************************************/
    			$five_weeks_and_6_days_after_event = new \DateTime($event['date']);
    			$five_weeks_and_6_days_after_event->modify('+41 days');
    			$five_weeks_and_6_days_after_event = strtotime($five_weeks_and_6_days_after_event->format('Y-m-d')); 
    			 if($current_date == $five_weeks_and_6_days_after_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "5 Weeks and 6 Days from Exam Start" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo '5 weeks and 6 days after done';
    			     }
    			    }
    			}
    			/*******************************************************************************/
    			$six_weeks_after_event = new \DateTime($event['date']);
    			$six_weeks_after_event->modify('+42 days');
    			$six_weeks_after_event = strtotime($six_weeks_after_event->format('Y-m-d')); 
    			 if($current_date == $six_weeks_after_event){
    				echo $event['title'] . ' is going to get mail<br/>';
    				echo $event['event_id'] . '<br/>';
    				$clients = client::all()->where('eventbrite_id' , $event['event_id']);
    			    echo count($clients) . '<br/>';
    			  if (count($clients) > 0){
    			   foreach ($clients as $client){
    			   echo $client['email'] .'<br/>';
    			   echo  md5($client['email']) .'<br/><br/>';
    			   $fields =array('tags'  => array( ["name"=> "Exactly at the 6 weeks from the exams being available" , "status" => "active"]));
		           $fields_string = json_encode($fields);
				   $curl = curl_init();
				   curl_setopt_array($curl, array(
				   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/". md5($client['email']) ."/tags",
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => "",
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
				   CURLOPT_CUSTOMREQUEST => "POST",
				   CURLOPT_POSTFIELDS => $fields_string,
				   ));
				   $response = curl_exec($curl);
				   $err = curl_error($curl);
				   curl_close($curl);
				   var_dump($err);
				   var_dump($response);
				   echo 'six weeks after done';
    			     }
    			    }
    			}
    			/*******************************************************************************/




    		}
    	}

    	/*
    	// check if the member is integrated to mailchimp and if the md5 is working  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/339c6033e1f551589495f176ee2a33f5/tags');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		curl_setopt($ch, CURLOPT_USERPWD, 'anystring' . ':' . 'b9e545cf953dfa1f101e97c7d02d3eaa-us17');

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		//var_dump($result);
		//print("<pre>".print_r($result,true)."</pre>");


		//Adding the specific Tag label to memebers eventbrite account. 
		  $fields =array(
              'tags'  => array( ["name"=> "Exactly at the 6 weeks from the exams being available" , "status" => "active"]  )
	       );

	       $fields_string = json_encode($fields);

		$curl = curl_init();
		   curl_setopt_array($curl, array(
		   CURLOPT_URL => "https://us17.api.mailchimp.com/3.0/lists/754a35c9e2/members/339c6033e1f551589495f176ee2a33f5/tags",
		   CURLOPT_RETURNTRANSFER => true,
		   CURLOPT_ENCODING => "",
		   CURLOPT_MAXREDIRS => 10,
		   CURLOPT_TIMEOUT => 30,
		   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		   CURLOPT_USERPWD => "apikey:b9e545cf953dfa1f101e97c7d02d3eaa-us17",
		   CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => $fields_string,
		   ));
		   $response = curl_exec($curl);
		   $err = curl_error($curl);
		   curl_close($curl);
		   var_dump($err);
		   var_dump($response);
		  */

    	}


  public function memberIntegartion()
    {
    
 	$events = event::all()->where('publish','1')->where('eventbrite','1')->where('test','0')->sortBy('date');
 	//echo $events;
 	$events = json_decode($events,true);
 	foreach ($events as $event) {
 	  //getting events info from Id : 
 	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/".$event['event_id']."/attendees/?token=HVI4TKUB4HG4AA25SWQN");
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	  curl_setopt($ch, CURLOPT_HEADER, FALSE);
	      
	  $response = curl_exec($ch);
	  //echo $response;
	  curl_close($ch);
	  $results = json_decode($response,true);
	  if (isset($results['attendees'])) {
	  	foreach ($results['attendees'] as $profile) {
	     if ($profile['status'] == 'Deleted'){
	      continue;
	     }
		
	    //check if member exists
	    $existing_members = client::all();
	    $existing_members = json_decode($existing_members,true);
	    foreach ($existing_members as $existing_member) {
	     if($profile['id'] == $existing_member['eventbrite_id']){
	      echo ' This member already exists ' . $profile['id']  . '<br/><br/>';
          continue;
	     }else{
	      $eventbrite_id = $profile['id'];
	      $first_name = $profile['profile']['first_name'];
	      $last_name =  $profile['profile']['last_name'];
	      $email = $profile['profile']['email'];
	      if(isset($profile['profile']['cell_phone'])){
	      	$phone = $profile['profile']['cell_phone'];
	      }else{
	      	$phone = 'empty';
	      }
	      if(isset($profile['profile']['home'])){
	        $home = $profile['profile']['home'];
	      }else{
	      	$home = 'empty';
	      }
	      if(isset($profile['profile']['company'])){
	        $company = $profile['profile']['company'];
	      }else{
	      	$company = 'empty';
	      }
	      if(isset($profile['profile']['gender'])){
	        $gender = $profile['profile']['gender'];
	      }else{
	      	$gender = 'empty';
	      }
	      //Adding attendee to lms    
	      $fields = array(
		  'first_name' => $first_name,
		  'last_name' => $last_name,
		  'email' => $email,
		  'login' => str_replace(' ', '-', $first_name). rand(),
		  'password' => str_replace(' ', '-', $last_name)
		  );
   		  $fields_string = http_build_query($fields);
	      $curl = curl_init();
	      curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/usersignup",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $fields_string,
		  ));
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  curl_close($curl);
		  if ($err) {
		  echo "cURL Error #:" . $err;
		  } else {
		  echo $response;
		  }
		  $response = json_decode($response, true);
		  // assgin to group
		  if (isset($response['id'])){
          $id = $response['id'];
          $key = $event['lms_key'];
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "https://myautismfitness.talentlms.com/api/v1/addusertogroup/user_id:". $id .",group_key:" . $key );
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
          curl_setopt($ch, CURLOPT_USERPWD,  'tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY');
          $result = curl_exec($ch);
          if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $assinged_to_group = 0;
          }
          curl_close($ch);
          echo $result;
          $result =json_decode($result, true);
          // adding L1 toolbox 
          if(isset($result['user_id'])){
		   echo '<br/>user successfully added <br/>';
		   $assinged_to_group = 1;
		   $fields = array(
	  	   'user_id' => $id,
	       'course_id' => '125'
	       );
	       $fields_string = http_build_query($fields);
		   $curl = curl_init();
		   curl_setopt_array($curl, array(
		   CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/addusertocourse",
		   CURLOPT_RETURNTRANSFER => true,
		   CURLOPT_ENCODING => "",
		   CURLOPT_MAXREDIRS => 10,
		   CURLOPT_TIMEOUT => 30,
		   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		   CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
		   CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => $fields_string,
		   ));
		   $response = curl_exec($curl);
		   $err = curl_error($curl);
		   curl_close($curl);
		   if ($err) {
		   echo "cURL Error #:" . $err;
		   $l1_toolbox_assigned = 0;
		   } else {
		   //echo "ok";
		   	echo $response;
		   	$l1_toolbox_assigned = 1;
		   	//$assinged_to_group  // $l1_toolbox_assigned // $l1_exams_assigned
			$client = new Client();
	          $client->eventbrite_id = $eventbrite_id;
	          $client->lms_id = $id;
	          $client->lms_group_id = $event['event_id'];
	          $client->first_name = $first_name;
	          $client->last_name = $last_name;
	          $client->cell_phone = $phone;
	          $client->address1 = $home;
	          $client->address2 = $home;
	          $client->city = $home;
	          $client->state = $home;
	          $client->country = $home;
	          $client->country_code = $home;
	          $client->zip = $home;
	          $client->Gender = $gender;
	          $client->l1_p1_exam_passed = 0;
	          $client->l1_p2_exam_passed = 0;
	          $client->l1_p1_exam_score = 0;
	          $client->email = $email;
	          $client->lms_email = $email;
	          $client->assinged_to_group = $assinged_to_group;
	          $client->l1_toolbox_assigned = $l1_toolbox_assigned;
	          $client->l1_exams_assigned = 0;
			  $client->save();
			echo 'user added to Database <br>';
		   }
		  

		  } 

      	  }else{
      	  	$id ='not_reg_or_exists';
      	  	$assinged_to_group = 0;
      	  	$l1_toolbox_assigned = 0;
      	  }
      	


      	  }
	     }
	    }
	   }
	  }
	}



    public function eventChecker()
    {
    // ### Checks if LMS has the event group key/ID and ads it to the database, Assigns L1 Courses to Group ###
  	// Adding Group key 
    $events = event::all()->where('publish','1')->where('eventbrite','1')->where('lms_key','empty');
 	$events = json_decode($events,true);
 	foreach ($events as $event) {
 		echo 'test work';
 		$arr = file_get_contents("http://ambasd.tw1.ru/f_json"); 
        $arr = json_decode($arr, TRUE);
        foreach ($arr as $key => $value) {
          if (isset($value[$event['event_id']])) {
            $lmsGroupKey = $value[$event['event_id']];
            echo 'event with ID  '. $lmsGroupKey . ' Has been Updated <br/>' ;
            event::where('event_id' , $event['event_id'])->update(['lms_key' => $lmsGroupKey]); 
          }              
        }
     }
     echo 'group key set <br/>'; 
     // Adding group_id 
     $events = event::all()->where('publish','1')->where('eventbrite','1');
     $events = json_decode($events,true);
     foreach ($events as $event) {
      if($event['lms_group_id'] == 'empty' && !empty($event['lms_key'])){
         $groupKey = $event['lms_key'];
		  $curl = curl_init();
		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/groups" ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
		  CURLOPT_CUSTOMREQUEST => "GET",
		  ));
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  curl_close($curl);
		  if ($err) {
		  echo "cURL Error #:" . $err;
		  } else {
		    //echo "ok";
		  }
		  $groups = json_decode($response,true);
		  foreach ($groups as $group){
		  	if ($group['key'] == $groupKey ){
		  		echo 'group id is ' . $group['id'] .'<br/>';
		  		event::where('lms_key' , $groupKey)->update(['lms_group_id' => $group['id']]); 
		  	}
		  }
      }
     }
     echo 'group ID set <br/>'; 
     //Assigning L1 Courses to Events
     $events = event::all()->where('publish','1')->where('eventbrite','1')->where('l1_course_added','0');
     $courses = course::all()->where('stage','L1');
 	 foreach ($events as $event) {
 	  //foreach ($courses as $course) {
 	   $fields = array(
  	   'course_id' => '125',
       'group_id' => $event['lms_group_id']
       );
       $fields_string = http_build_query($fields);
 	   $curl = curl_init();
	   curl_setopt_array($curl, array(
	   CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/addcoursetogroup",
	   CURLOPT_RETURNTRANSFER => true,
	   CURLOPT_ENCODING => "",
	   CURLOPT_MAXREDIRS => 10,
	   CURLOPT_TIMEOUT => 30,
	   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	   CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
	   CURLOPT_CUSTOMREQUEST => "POST",
	   CURLOPT_POSTFIELDS => $fields_string,
	   ));
	   $response = curl_exec($curl);
	   $err = curl_error($curl);
	   curl_close($curl);
	   if ($err) {
	   echo "cURL Error #:" . $err;
	   } else {
	   //echo "ok";
	   	echo $response;
	   }
 	 // }
 	  event::where('lms_group_id' , $event['lms_group_id'])->update(['l1_course_added' => '1']); 
 	 }
 	 echo 'Course Assigning set <br/>';

    }






public function hook(Request $request)
    {
   		//echo $request;
    
    }


public function levelOneCoursesUpdates(Request $request)
    {
   	 $events = event::all()->where('publish','1')->where('eventbrite','1');
 	 $events = json_decode($events,true);
 	 $courses = course::all()->where('stage','L1');
 	 $courses = json_decode($courses,true);
     foreach ($events as $event) {
 	   echo 'Event date is ' .  $event['date'] . ' in timestamp would be '. strtotime($event['date'])  .'<br/>';
 	   $tz_obj = new \DateTimeZone($event['time_zone']);
       $now = new \DateTime("now", $tz_obj);
       $now = $now->format('Y-m-d H:i:s');
       $now  = strtotime($now); 
       $date = strtotime($event['date']); // event start date
       $event_should_be_passed = strtotime('+1 day' ,$date); // approx event end date to stop rechecking
 	   //echo 'Today is ' . $now . '<br/>';
 	  if($date < $now ){
 	  	//if events are old we pass
 	   	if($event_should_be_passed < $now ){
 	   	 echo '24 hours past no need to check this event <br/>';
 	   	}else{
 	   	 echo 'this event has ended: ' . $event['title'] . '<br/>';
 	   	 foreach ($courses as $course) {
 	   	  if($course['course_id'] != '125' && $course['stage'] == 'L1' ){
 	   	  	echo ' Name ' . $course['name'] . '<br/>';
 	   	   // adding courses	
 	   	   $fields = array(
	  	   'course_id' => $course['course_id'],
	       'group_id' => $event['lms_group_id']
	       );
	       $fields_string = http_build_query($fields);
	 	   $curl = curl_init();
		   curl_setopt_array($curl, array(
		   CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/addcoursetogroup",
		   CURLOPT_RETURNTRANSFER => true,
		   CURLOPT_ENCODING => "",
		   CURLOPT_MAXREDIRS => 10,
		   CURLOPT_TIMEOUT => 30,
		   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		   CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
		   CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => $fields_string,
		   ));
		   $response = curl_exec($curl);
		   $err = curl_error($curl);
		   curl_close($curl);
		   if ($err) {
		   echo "cURL Error #:" . $err;
		   } else {
		   //echo "ok";
		   	echo $response;
		   } 
		  //enrolling courses to group member 
		    //getting group members : 
		  $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "https://myautismfitness.talentlms.com/api/v1/groups?id=". $event['lms_group_id'] );
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
          curl_setopt($ch, CURLOPT_USERPWD,  'tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY');
          $result = curl_exec($ch);
          if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
          }
          curl_close($ch);

          $group = json_decode($result,true);
          foreach ($group['users'] as $user) {
          	//echo 'User id is ' . $user['id'] . '<br/>';
          	echo $course['course_id'];
          	$field = array(
	  	   'user_id' => $user['id'],
	       'course_id' => $course['course_id']
	       );
	       $fields_s = http_build_query($field);
		   $curl = curl_init();
		   curl_setopt_array($curl, array(
		   CURLOPT_URL => "https://myautismfitness.talentlms.com/api/v1/addusertocourse",
		   CURLOPT_RETURNTRANSFER => true,
		   CURLOPT_ENCODING => "",
		   CURLOPT_MAXREDIRS => 10,
		   CURLOPT_TIMEOUT => 30,
		   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		   CURLOPT_USERPWD => "tjXG9vthASh5IAEy88qZVF8tBuZRpV:Mzu4hSly1Q2thDDejpF9ChvfWHpAYAYYYY",
		   CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => $fields_s,
		   ));
		   $response = curl_exec($curl);
		   //echo $response;
		   $response = json_decode($response,true);
		   $err = curl_error($curl);
		   curl_close($curl);
		   if ($err) {
		   echo "cURL Error #:" . $err;
		   } else {
		   	//$eventid = $event['lms_group_id'];
		   	$members = Client::all();
          	$members = json_decode($members,true);
          	foreach ($members as $member) {
          	 foreach ($response as $resp) {
		   	  if(isset($resp['type'])){
		   	    $error = 'error';
		   	  }
		     }
		   	 if(isset($error)){
		   	 }else{
		   	    if($member['lms_id'] == $user['id']){
		   	    echo 'User course enrolled';
          	  	client::where('lms_id' , $user['id'])->update(['l1_exams_assigned' => '1']); 
          	  }
		   	 }				
          	}
           }
       	  }

  	   	  }
 	   	 }
 	   	}
 	  }    
     }
    }








}



