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

	function checkUser($email){

		$cursor = selection("users","");
		foreach($cursor as $task)
		{
			if($task['username']==$email)
			{
				//Change status to 2 if email id is already registered and show toast message
				return true;
			}else{
				return false;
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

	//Webservice to update GCM for the profile
	function updateGcm($id,$gcm){
		$doc = array();
		$doc['_id'] = intval($id);

		$update = array();
		$update['gcm'] = $gcm;

		updation("users",$doc,$update);

		$str = "{\"status\":1}";
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

	function deletePlan($eventId){
		$doc = array();
		$doc['_id'] = intval($eventId);
		
		$sel = array();
		$sel['event_id'] = intval($eventId);

		deletion("events",$doc);
		deletion("notifications",$sel);

		$str = "{\"status\":1}";

		return $str;	
	}
	
	//Webservice function to get all plans created by the user
	function viewPlans($id){
		$doc = array();
		$doc['user_id'] = intval($id);

		$cursor = selection("events",$doc);
		$i=0;

		//Conversion to the json format required
		$str = "{\"plans\":[";
		foreach($cursor as $task)
		{
				$str.="{\"id\":\"".$task['_id']."\",";	
				$str.="\"title\":\"".$task['title']."\"},";
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
		$str .= "]}";

		return $str;
	}

	//Webservice to get single plan
	function viewSinglePlan($id){
		$doc = array();
		$doc['_id'] = intval($id);

		$cursor = selection("events",$doc);
		$i=0;

		//Conversion to the json format required
		$str = "";
		foreach($cursor as $task)
		{
				$str.="{\"id\":\"".$task['_id']."\",";	
				$str.="\"title\":\"".$task['title']."\",";
				$str.="\"description\":\"".$task['description']."\",";
				$str.="\"price\":\"".$task['price']."\",";
				$str.="\"date\":\"".$task['date']."\",";
				$str.="\"time\":\"".$task['time']."\",";
				$str.="\"location\":\"".$task['location']."\",";
				$str.="\"latitude\":\"".$task['latitude']."\",";
				$str.="\"longitude\":\"".$task['longitude']."\"}";
				$i++;
		}

		if($i==0)
		{
			$str.="{}";
		}

		return $str;	
	}

	//Webservice to get all notifications with respect to events
	function viewNotifications($id){
		$doc = array();
		$doc['user_id'] = intval($id);

		$cursor = selection("notifications",$doc);
		$i=0;

		//Conversion to the json format required
		$str = "{\"plans\":[";
		foreach($cursor as $task)
		{
				$str.="{\"id\":\"".$task['_id']."\",";
				$str.="\"eventid\":\"".$task['event_id']."\",";
				$str.="\"description\":\"".$task['description']."\",";
				$str.="\"title\":\"".$task['title']."\"},";
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
		$str .= "]}";

		return $str;	
	}

	//View notification webservice
	function viewSingleNotification($id){
		$doc = array();
		$doc['_id'] = intval($id);

		$cursor = selection("notifications",$doc);
		$i=0;
		$str = "";

		//Conversion to the json format required
		foreach($cursor as $task)
		{
				$str.="{\"id\":\"".$task['_id']."\",";
				$str.="\"eventid\":\"".$task['event_id']."\",";
				$str.="\"description\":\"".$task['description']."\",";
				$str.="\"user_id\":\"".$task['user_id']."\",";
				$str.="\"title\":\"".$task['title']."\"}";
				$i++;
		}

		if($i==0)
		{
			$str.="{}";
		}
	
		return $str;
	}

	//Webservice to create function
	function createMessage($sid,$susername,$rusername,$message){
		for($cnt = 0;$cnt < 2; $cnt++){
			$doc = array();
			//$doc['sid']  = intval($sid);
			if($cnt==0){
				$doc['susername'] = $susername;
				$doc['rusername'] = $rusername;
			}else{
				$doc['susername'] = $rusername;
				$doc['rusername'] = $susername;
			}
		

			$rdoc = array();
			$rdoc['username'] = $rusername;

			$messages = array();

			$i = 0;
			$j = 0;

			date_default_timezone_set("Asia/Kolkata");
			
			$sDate = date('d/m/y');
			$sTime = date("h:i:s");

			$cursor = selection("users",$rdoc);
			foreach($cursor as $task)
			{
				$doc['rid'] = intval($task['_id']);
				$i++;
			}

			if($i==0){
				return "{\"status\":-1}";
			}

			$cursor2 = selection("messaging","");
			$big = 0;
			foreach($cursor2 as $task2)
			{
				$idd = intval($task2['_id']);
				if($idd>$big)
				{
					$big = $idd;
				}
				if(($task2['susername']==$susername)&&($task2['rusername']==$rusername))
				{
					$k = 0;
					$mCount = 0;
					$updation = array();
					$msg = array();
					
									
					//$updation['messages'] = $task2['messages'];
									
					foreach($task2['messages'] as $msgArray){

						$msg['id'] = $msgArray['id'];
						$msg['sid'] = $msgArray['sid'];
						$msg['message'] = $msgArray['message'];
						$msg['date'] = $msgArray['date'];
						$msg['time'] = $msgArray['time'];

						array_push($messages,$msg);
						unset($msg);
					}

					$newMessage = array();
					$newMessage['id'] = ++$k;
					$newMessage['sid'] = intval($sid);
					$newMessage['message'] = $message;
					$newMessage['date'] = $sDate;
					$newMessage['time'] = $sTime;

					array_push($messages,$newMessage);

					$updation['messages'] = $messages;
					updation("messaging",$doc,$updation);
					$j++;
					return "{\"status\":2}";
				}else if(($task2['susername']==$rusername)&&($task2['rusername']==$susername)){
					$k = 0;
					$mCount = 0;
					$updation = array();
					$msg = array();
					
									
					//$updation['messages'] = $task2['messages'];
									
					foreach($task2['messages'] as $msgArray){

						$msg['id'] = $msgArray['id'];
						$msg['sid'] = $msgArray['sid'];
						$msg['message'] = $msgArray['message'];
						$msg['date'] = $msgArray['date'];
						$msg['time'] = $msgArray['time'];

						array_push($messages,$msg);
						unset($msg);
					}

					$newMessage = array();
					$newMessage['id'] = ++$k;
					$newMessage['sid'] = intval($sid);
					$newMessage['message'] = $message;
					$newMessage['date'] = $sDate;
					$newMessage['time'] = $sTime;

					array_push($messages,$newMessage);

					$updation['messages'] = $messages;
					updation("messaging",$doc,$updation);
					$j++;
					return "{\"status\":2}";
				}
			}

			if(($j==0)&&($cnt==1)){
				$doc['_id'] = ++$big;
				$doc['sid']  = intval($sid);
				$messages['id'] = 1;
				$messages['sid'] = intval($sid);
				$messages['message'] = $message;
				$messages['date'] = $sDate;
				$messages['time'] = $sTime;

				$doc['tdate'] = $sDate;
				$doc['ttime'] = $sTime;

				//Verify this
				$doc['messages'] = array($messages);
				insertion("messaging",$doc);
				return "{\"status\":1}";
			}
		}
	}

	//Webservice function to delete the message thread
	function deleteMessage($susername,$rusername){
		$doc = array();
		$doc['susername'] = $susername;
		$doc['rusername'] = $rusername;

		deletion("messaging",$doc);

		$rdoc = array();
		$rdoc['susername'] = $rusername;
		$rdoc['rusername'] = $susername;
		
		deletion("messaging",$rdoc);
		
		return "{\"status\":1}";
	}

	//Webservice function to get a message thread
	function getMessage($susername,$rusername){
		for($k=0;$k<2;$k++){
			$doc = array();
			if($k==0){
				$doc['susername'] = $susername;
				$doc['rusername'] = $rusername;
			}else{
				$doc['susername'] = $rusername;
				$doc['rusername'] = $susername;
			}
			$i = 0;
			$str = "{\"messages\":[";
			$cursor = selection("messaging",$doc);
			foreach($cursor as $task)
			{
				foreach($task['messages'] as $value){
					$str.="{\"id\":\"".$value['sid']."\",";	
					$str.="\"message\":\"".$value['message']."\",";
					$str.="\"date\":\"".$value['date']."\"},";
					$i++;
				}
			}

			if(($i==0)&&($k==1))
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
	}

	//Webservice to send message notification to the user
	function sendMessageNotification($username,$message){
		$doc = array();
		$doc['username'] = $username;
		$gcmid = "";

		$cursor = selection("users",$doc);
		foreach($cursor as $task)
		{
			$gcmid = $task['gcm'];
		}

		return sendnotification($gcmid,"Messages",$message,"1","0");
	}

	//Get webservice inbox
	function getMessageInbox($username){
		$i = 0;
		$str = "{\"messages\":[";

		for($j=0;$j<2;$j++){
			$doc = array();
			if($j==0){
				$doc['susername'] = $username;
			}else{
				$doc['rusername'] = $username;
			}

			$cursor = selection("messaging",$doc);
			foreach ($cursor as $task) {
				$lastMsg = end($task['messages']);
				
				$username = "";
				if($j==0){
					$username = $task['rusername'];
				}else{
					$username = $task['susername'];
				}			

				$str.="{\"id\":\"".$lastMsg['sid']."\",";	
				$str.="\"message\":\"".$lastMsg['message']."\",";
				$str.="\"username\":\"".$username."\",";
				$str.="\"time\":\"".$lastMsg['time']."\",";
				$str.="\"date\":\"".$lastMsg['date']."\"},";
				$i++;
				break;
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

	//Webservice function to add contacts
	function addContact($id,$ausername){
		$doc = array();
		$doc['userid'] = intval($id);
		$storedContacts = array();
		$contacts = array();
		$str = "";

		if(!checkUser($ausername)){
			return "{\"status\":0}";
		}

		$cursor = selection("contacts","");
		$big = 0;
		foreach($cursor as $task)
		{
			$idd = intval($task['_id']);
			if($idd>$big)
			{
				$big = $idd;
			}

			
			if($id==$task['userid']){
				$k=0;
				foreach($task['contacts'] as $cnct){
					
					if($ausername==$cnct['username']){
						return "{\"status\":0}";
					}
					$storedContacts['id'] = $cnct['id'];
					$storedContacts['username'] = $cnct['username'];

					array_push($contacts,$storedContacts);
					unset($storedContacts);
					++$k;
				}

				$newContact = array();
				$newContact['id'] = ++$k;
				$newContact['username'] = $ausername;
				
				array_push($contacts,$newContact);

				$update = array();
				$update['contacts'] = $contacts;
						
				updation("contacts",$doc,$update);			
				
				return "{\"status\":1}";
			}
			
		}

		if($big==0){
			$doc['_id'] = ++$big;

			$contacts['id'] = 1;
			$contacts['username'] = $ausername;

			$doc['contacts'] = array($contacts);
			
			insertion("contacts",$doc);	

			return "{\"status\":1}";
		}
	}

	//Webservice function to get Contacts
	function getContacts($id){
		$doc = array();
		$doc['userid'] = intval($id);
		$i=0;
		$str = "{\"contacts\":[";

		$cursor = selection("contacts",$doc);
		foreach($cursor as $task)
		{
			foreach($task['contacts'] as $cnct){	
				$sel = array();
				$sel['username'] = $cnct['username'];

				$phone = "";
				$sid = "";
				$cur = selection("users",$sel);

				foreach($cur as $tsk){
					$phone = $tsk['phone'];
					$sid = $tsk['_id'];
				}
				$str.="{\"id\":\"".$cnct['id']."\",";	
				$str.="\"username\":\"".$cnct['username']."\",";
				$str.="\"sid\":\"".$sid."\",";
				$str.="\"phone\":\"".$phone."\"},";
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

	//Webservice function to remove contact
	function deleteContact($id,$ausername){
		$doc = array();
		$doc['userid'] = intval($id);
		$storedContacts = array();
		$contacts = array();
		$i=0;
		
		$cursor = selection("contacts",$doc);
		foreach($cursor as $task)
		{
			foreach($task['contacts'] as $cnct){
					
				if($ausername==$cnct['username']){
					$i++;
					continue;
				}
				$storedContacts['id'] = $cnct['id'];
				$storedContacts['username'] = $cnct['username'];

				array_push($contacts,$storedContacts);
				unset($storedContacts);
			}
			if($i==0){
				return "{\"status\":0}";
			}
			$update = array();
			$update['contacts'] = $contacts;
						
			updation("contacts",$doc,$update);			
				
			return "{\"status\":1}";
		}
	}

	//Webservice function to send notification
	function sendnotification($gid, $msg,$description,$imsg,$eventId) {
        $registatoin_ids = array($gid);
		$message = array("m" => $msg,"eventid" => $eventId,"description" => $description,"imsg" => $imsg);
		
		// Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
			
        );
		// Google Cloud Messaging GCM API Key  
		define("GOOGLE_API_KEY", "AIzaSyC3zQp4SUdAicdmI54gLn0aqWJiutch264");
								 
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
