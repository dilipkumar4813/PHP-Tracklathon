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
			array("id"=>"xsd:string","firstname"=>"xsd:string","lastname"=>"xsd:string","emailid"=>"xsd:string","phone"=>"xsd:string","password"=>"xsd:string",),		//Input sent to the function if it is array() then input is zero
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
				'setLocation',	//Function that is being called
				array("id"=>"xsd:string","latitude"=>"xsd:string","longitude"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$server->register(
				'getLocation',	//Function that is being called
				array("id"=>"xsd:string"),
				array("return"=>"xsd:string") //Ouput datatype
			);

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA)? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA); 
?>