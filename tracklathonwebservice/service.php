<?php
	//Including the main functions that perform the operations
	require 'functions.php';
	
	//Call SOAP library 
	require_once ('lib/nusoap.php'); 

	//using nusoap_server to create server object 
	$server = new nusoap_server();
	
	//Configuration point to root folder that contains the web service
	$server->configureWSDL("tracklathonwebservice","urn:tracklathonwebservice"); 
	
	$server->register(
			'registration',	//Function that is being called
			array("firstname"=>"xsd:string","lastname"=>"xsd:string","emailid"=>"xsd:string","password"=>"xsd:string","gcmid"=>"xsd:string"),		//Input sent to the function if it is array() then input is zero
			array("return"=>"xsd:string") //Ouput datatype
		);

	$server->register(
				'checkUserExists',	//Function that is being called
				array("email"=>"xsd:string"),	//Input sent to the function if it is array() then input is zero
				array("return"=>"xsd:string") //Ouput datatype
			);
	
	$server->register(
				'forgotPassword',	//Function that is being called
				array("id"=>"xsd:string", "newPassword"=>"xsd:string"),	//Input sent to the function if it is array() then input is zero
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
			'updateProfile',	//Function that is being called
			array("id"=>"xsd:string","firstname"=>"xsd:string","lastname"=>"xsd:string","emailid"=>"xsd:string","phone"=>"xsd:string","password"=>"xsd:string"),		//Input sent to the function if it is array() then input is zero
			array("return"=>"xsd:string") //Ouput datatype
		);

	$server->register(
				'userAuthentication',	//Function that is being called
				array("emailid"=>"xsd:string", "password"=>"xsd:string"),//Input sent to the function if it is array() then input is zero
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getProfile',	//Function that is being called
				array("emailid"=>"xsd:string"),					  //Input sent to the function if it is array() then input is zero
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'updateGcm',	//Function that is being called
				array("id"=>"xsd:string","gcm"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'setLocation',	//Function that is being called
				array("id"=>"xsd:string","latitude"=>"xsd:string","longitude"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getLocation',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getLocationUsers',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getYourLocationViewer',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'shareLocation',	//Function that is being called
				array("id"=>"xsd:string","username"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'removeShareLocation',	//Function that is being called
				array("id"=>"xsd:string","sid"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'createPlan',	//Function that is being called
				array("id"=>"xsd:string","title"=>"xsd:string","description"=>"xsd:string","price"=>"xsd:string","contacts"=>"xsd:string","location"=>"xsd:string","latitude"=>"xsd:string","longitude"=>"xsd:string","date"=>"xsd:string","time"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'editPlan',	//Function that is being called
				array("eventId"=>"xsd:string","id"=>"xsd:string","title"=>"xsd:string","description"=>"xsd:string","price"=>"xsd:string","contacts"=>"xsd:string","location"=>"xsd:string","latitude"=>"xsd:string","longitude"=>"xsd:string","date"=>"xsd:string","time"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'deletePlan',	//Function that is being called
				array("eventId"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'viewPlans',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'viewSinglePlan',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'viewNotifications',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'viewSingleNotification',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'sendnotification',	//Function that is being called
				array("gid"=>"xsd:string","msg"=>"xsd:string","description"=>"xsd:string","imsg"=>"xsd:string","eventId"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'createMessage',	//Function that is being called
				array("sid"=>"xsd:string","susername"=>"xsd:string","rusername"=>"xsd:string","message"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'deleteMessage',	//Function that is being called
				array("susername"=>"xsd:string","rusername"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getMessage',	//Function that is being called
				array("susername"=>"xsd:string","rusername"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA)? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA); 
?>