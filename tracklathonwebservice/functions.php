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
		$doc['password'] = md5($password);
		$doc['last_login'] = date("Y/m/d");
		$doc['gcmid'] = $gcmid;

		insertion("users",$doc);
			
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

	function updateProfile($id,$firstname,$lastname,$emailid,$password){
		$sel = array();
		$sel['_id'] = intval($id);

		$doc = array();
		$doc['firstname'] = $firstname;
		$doc['lastname'] = $lastname;
		$doc['username'] = $emailid;

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
	function getProfile($id){
		$doc = array();
		$doc['_id'] = intval($id);

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
