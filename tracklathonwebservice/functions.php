<?php
	
	include "database.php";
	
	//Webservice for registration
	function registration($firstname,$lastname,$emailid,$password,$gcmid)
	{
		$cursor = selection("users","");
		$big = -1;
		foreach($cursor as $task)
		{
			$idd = intval($task['_id']);
			if($idd>$big)
			{
				$big = $idd;
			}
			if($task['username']==$emailid)
			{
				//Change status to 2 if email id is already registered and show toast message
				$str = "{\"status\":0}";
				goto a;
			}
		}
		
		
		$big++;
		$doc = array();
		$doc['_id'] = intval($big);
		$doc['firstname']= $firstname;
		$doc['lastname']= $lastname;
		$doc['username'] = $emailid;
		$doc['phone'] = "";
		$doc['password'] = md5($password);
		$doc['last_login'] = date("Y/m/d");
		$doc['gcmid'] = $gcmid;

		insertion("users",$doc);

		$empty = array();
		$location = array();
		$location['_id'] = intval($big);
		$location['viewercontacts'] = $empty;
		$location['contacts'] = $empty;
		insertion("location",$location);
			
		//$doc1 = array();
		//$doc1['_id'] = intval($big);
		
		$str = "{\"status\":1}";
		
		a:
		return $str;
	}

	//Webservice to check if the user exits in the forgot password screen
	function checkUserExists($email){

		$cursor = selection("users","");
		foreach($cursor as $task)
		{
			if($task['username']==$email)
			{
				//Change status to 2 if email id is already registered and show toast message
				$str = "{\"status\":1}";
				return $str;
			}else{
				$str = "{\"status\":0}";
				return $str;
			}
		}
	}

	//Webservice to change the password using the forgot password screen
	function forgotPassword($id,$newPassword){
		$sel = array();
		$sel['_id'] = intval($id);

		$doc = array();
		$doc['password'] = md5($newPassword);

		updation("users",$sel,$doc);

		$str = "{\"status\":1}";
		return $str;
	}

	function updateProfile($id,$firstname,$lastname,$emailid,$phone,$password){
		$sel = array();
		$sel['_id'] = intval($id);

		$doc = array();
		$doc['firstname'] = $firstname;
		$doc['lastname'] = $lastname;
		$doc['username'] = $emailid;
		$doc['phone'] = $phone;

		updation("users",$sel,$doc);
	
		$str = "{\"status\":1}";
		return $str;
	}

	//Function responsible for authentication
	function userAuthentication($emailid,$password)
	{
		$pwd = md5($password);
		$cursor = selection("users","");
		$str = "";
		foreach($cursor as $task)
		{
			if(($task['username']==$emailid)&&($task['password']==$pwd))
			{
				$str = "{\"status\":1}";
				break;
			}
			else
			{
				$str = "{\"status\":0}";
			}
		}
		
		return $str;
	}

	//Webservice to get user profile
	function getProfile($emailid){
		$doc = array();
		$doc['username'] = $emailid;

		$str = "";

		$cursor = selection("users",$doc);
		$i = 0;
		
		//Conversion to the json format required
		foreach($cursor as $task)
		{
			$str.= "{\"_id\":".$task['_id'];
			$str.= ",\"firstname\":\"".$task['firstname'];
			$str.= "\",\"lastname\":\"".$task['lastname'];
			$str.= "\",\"username\":\"".$task['username'];
			$str.= "\",\"phone\":\"".$task['phone'];
			$str.= "\",\"gcmid\":\"".$task['gcmid']."\"}";
			$i++;
		}
		
		if($i==0)
		{
			$str.="{}";
		}
		else
		{
			$str = rtrim($str, ",");
		}
		
		return $str;
	}

	//Webservice to set user location
	function setLocation($id,$latitude,$longitude){
		$sel = array();
		$sel['_id'] = intval($id);

		$cursor = selection("location",$sel);
		$i = 0;

		foreach($cursor as $task)
		{
			$i++;
			$doc = array();
			$doc['latitude'] = $latitude;
			$doc['longitude'] = $longitude;
			updation("location",$sel,$doc);
		}

		if($i==0){
			$ins = array();
			$ins['_id'] = intval($id);
			$ins['latitude'] = $latitude;
			$ins['longitude'] = $longitude;

			insertion("location",$ins);
		}

		$str = "{\"status\":1}";
		return $str;

	}

	//Webservice to get user location information
	function getLocation($id){
		$sel = array();
		$sel['_id'] =  intval($id);
		$str  = "";

		$cursor = selection("location",$sel);
		$i = 0;

		foreach($cursor as $task)
		{
			$i++;
			$doc = array();
			$str = "{\"id\":".$task['_id'];
			$str.= ",\"latitude\":\"".$task['latitude']."\"";
			$str.= ",\"longitude\":\"".$task['longitude']."\"}";
		}

		if($i==0){
			$str = "{}";
		}

		return $str;
	}

	//Webservice to get all the users
	function getLocationUsers($id){

		$doc = array();
		$doc['_id'] = intval($id);
		$cursor = selection("location",$doc);
		$i = 0;
		$username="";
		
		//Conversion to the json format required
		$str = "{\"contacts\":[";
		foreach($cursor as $task)
		{
			
			foreach($task['contacts'] as $key => $value){
				$str.="{\"id\":\"".$key."\",";
				$str.="\"name\":\"".$value."\"},";
				$i++;
			}
		}

		if($i==0)
		{
			$str.="{}";
		}
		else
		{
			$str = rtrim($str, ",");
		}
		$str .= "]}";

		return $str;
	}

	//Webservice to get all the user that can view your location
	function getYourLocationViewer($id){
		$doc = array();
		$doc['_id'] = intval($id);
		$cursor = selection("location",$doc);
		$i = 0;
		$username="";
		
		//Conversion to the json format required
		$str = "{\"contacts\":[";
		foreach($cursor as $task)
		{
			
			foreach($task['viewercontacts'] as $key => $value){
				$str.="{\"id\":\"".$key."\",";
				$str.="\"name\":\"".$value."\"},";
				$i++;
			}
		}

		if($i==0)
		{
			$str.="{}";
		}
		else
		{
			$str = rtrim($str, ",");
		}
		$str .= "]}";

		return $str;
	}

	//Webservice to share the user location
	function shareLocation($id,$username){
		$doc = array();
		$doc['username'] = $username;
		$sel = array();
		$sid = "";
		$str = "";
		$yourUsername = "";

		$cursor = selection("users",$doc);
		$i = 0;
		
		//Conversion to the json format required
		foreach($cursor as $task)
		{
			
			$sid = $task['_id'];
			$i++;
		}

		$yourDetails = array();
		$yourDetails['_id'] = intval($id);

		$cursorYours = selection("users",$yourDetails);
		$j = 0;
		
		//Conversion to the json format required
		foreach($cursorYours as $taskYours)
		{
			
			$yourUsername = $taskYours['username'];
			$j++;
		}

		

		if(($i==0)||($j==0)){
			$str = "{\"status\":0}";
			return $str;
		}else{
			$sel['_id'] = intval($sid);

			$contacts = array();
			$contacts[$id] = $yourUsername;

			$cursorContacts = selection("location",$sel);
			
			foreach($cursorContacts as $task1)
			{
				foreach($task1['contacts'] as $key => $value){
					$contacts[$key] = $value;			
				}
			}

			$update = array();
			$update['contacts'] = $contacts;

			updation("location",$sel,$update);

			//Add the members who can view you into your location table
			$yourAccount = array();
			$yourAccount['_id'] = intval($id);

			$yourViewers = array();
			$yourViewers[$sid] = $username;
			
			$cursorContactsViewers = selection("location",$yourAccount);
			
			foreach($cursorContactsViewers as $task2)
			{
				foreach($task2['viewercontacts'] as $key => $value){
					$yourViewers[$key] = $value;			
				}
			}

			$viewers = array();
			$viewers['viewercontacts'] = $yourViewers;
			
			updation("location",$yourAccount,$viewers);

			$str = "{\"status\":1}";
			return $str;
		}
	}

	//Webservice to unshare the user location
	function removeShareLocation($id,$sid){
		$doc = array();
		$doc['_id'] = intval($sid);
		$cursor = selection("location",$doc);
		$i = 0;

		$update = array();
		$contacts = array();

		//Conversion to the json format required
		foreach($cursor as $task)
		{
			
			foreach($task['contacts'] as $key => $value){
				if($key != $id){
					$contacts[$key] = $value;
				}else{
					$i++;
				}
				
			}
		}

		$doc2 = array();
		$doc2['_id'] = intval($id);
		$j=0;
		$cursorYours = selection("location",$doc2);

		$updateYours = array();
		$contactsYours = array();

		//Conversion to the json format required
		foreach($cursorYours as $task2)
		{
			
			foreach($task2['viewercontacts'] as $key => $value){
				if($key != $sid){
					$contactsYours[$key] = $value;
				}else{
					$j++;
				}
				
			}
		}

		if(($i==0)||($j==0))
		{
			$str = "{\"status\":0}";
		}
		else
		{
			$update['contacts'] = $contacts;
			updation("location",$doc,$update);

			$updateYours['viewercontacts'] = $contactsYours;
			updation("location",$doc2,$updateYours);
			
			$str = "{\"status\":1}";
		}

		return $str;
	}

	//Webservice to create an trip
	function createPlan($id,$title,$description,$price,$contacts,$location,$latitude,$longitude,$date,$time){
		
		$cursor = selection("events","");
		$big = 0;

		foreach($cursor as $task)
		{
			$idd = intval($task['_id']);
			if($idd>$big)
			{
				$big = $idd;
			}
		}

		$groupMembers = array();
		

		$initial = array();
		$initial = explode(",",$contacts);

		foreach($initial as $separate){
			$sp = array();
			$sp = explode(".",$separate);
			
			$groupMembers[$sp[0]]=$sp[1];	
		}

		$big++;
		$doc = array();
		$doc['_id'] = intval($big);
		$doc['user_id'] = intval($id);
		$doc['title'] = $title;
		$doc['description'] = $description;
		$doc['price'] = $price;
		$doc['group'] = $groupMembers;
		$doc['location'] = $location;
		$doc['latitude'] = $latitude;
		$doc['longitude'] = $longitude;
		$doc['date'] = $date;
		$doc['time'] = $time;

		insertion("events",$doc);

		//Notifications table
		$cursor2 = selection("notifications","");
		$big2 = 0;

		foreach($cursor2 as $task2)
		{
			$idd2 = intval($task2['_id']);
			if($idd2>$big2)
			{
				$big2 = $idd2;
			}
		}

		$big2++;
		$notification = array();
		$notification['_id'] = intval($big2);
		$notification['title'] = "Plan created";
		$notification['description'] = $title;
		$notification['user_id'] = intval($id);
		$notification['event_id'] = intval($big);

		insertion("notifications",$notification);

		$str = "{\"status\":1}";

		return $str;
	}

	//Webservice to create an trip
	function editPlan($eventId,$id,$title,$description,$price,$contacts,$location,$latitude,$longitude,$date,$time){
		$sel = array();
		$sel['_id'] = intval($eventId);

		$groupMembers = array();
		

		$initial = array();
		$initial = explode(",",$contacts);

		foreach($initial as $separate){
			$sp = array();
			$sp = explode(".",$separate);
			
			$groupMembers[$sp[0]]=$sp[1];	
		}

		$doc = array();
		$doc['user_id'] = intval($id);
		$doc['title'] = $title;
		$doc['description'] = $description;
		$doc['price'] = $price;
		$doc['group'] = $groupMembers;
		$doc['location'] = $location;
		$doc['latitude'] = $latitude;
		$doc['longitude'] = $longitude;
		$doc['date'] = $date;
		$doc['time'] = $time;

		updation("events",$sel,$doc);

		//Notifications table
		$cursor2 = selection("notifications","");
		$big2 = 0;

		foreach($cursor2 as $task2)
		{
			$idd2 = intval($task2['_id']);
			if($idd2>$big2)
			{
				$big2 = $idd2;
			}
		}

		$big2++;
		$notification = array();
		$notification['_id'] = intval($big2);
		$notification['title'] = "Plan created";
		$notification['description'] = $title;
		$notification['user_id'] = intval($id);
		$notification['event_id'] = intval($eventId);
		
		insertion("notifications",$notification);

		$str = "{\"status\":1}";

		return $str;
	}
	
	function sendnotification($gid, $clientid,$jobid) {
        $registatoin_ids = array($gid);
		$message = array("m" => $clientid,"jobid" => $jobid);
		
		// Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
			
        );
		// Google Cloud Messaging GCM API Key  
		define("GOOGLE_API_KEY", "AIzaSyCO72wraXRvx6vnscloDNFpQ2eNEJO8kRY");
								 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json',
			'Connection: keep-alive',
			'Keep-Alive: 300'
		);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
		
        // Close connection
        curl_close($ch);
        return $result;
    }
	
?>
