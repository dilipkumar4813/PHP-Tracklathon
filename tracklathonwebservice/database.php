<?php
	
	//Creation connection to the database
	function dbConnection($tableName)
	{
		$dbName = "tracklathon";
			$uri = "mongodb://codeninja:THEbeast6@ds023530.mlab.com:23530/tracklathon";
			try { 
				$options = array("connectTimeoutMS" => 30000);
				$connection = new MongoClient($uri,$options);
				$database   = $connection->selectDB($dbName);
				$collection = $database->selectCollection($tableName);
				return($collection);
			}
			catch(MongoConnectionException $e)
			{
				die("Failed to connect to database ".$e->getMessage());
				return($e->getMessage());
			}
	}
	
	//Function responsible for insertion of document in the collection
	function insertion($tableName,$document)
	{
		try { 
			$collection = dbConnection($tableName);
			$collection->insert($document);
		}
		catch(MongoConnectionException $e)
		{
			die("Failed to connect to database ".$e->getMessage());
		}
	}
	
	//Function responsible for retrieving documents from the collection
	function selection($tableName,$condition)
	{
		$dbName = "tracklathon";
		try { 
			$connection = new MongoClient('mongodb://codeninja:THEbeast6@ds023530.mlab.com:23530/tracklathon');
			$database   = $connection->selectDB($dbName); 
			$collection = dbConnection($tableName);
			$collection = $database->selectCollection($tableName);
		}
		catch(MongoConnectionException $e)
		{
			die("Failed to connect to database ".$e->getMessage());
		}
			
		if($condition!="")
		{
			$cursor = $collection->find($condition);
		}
		else
		{
			$cursor = $collection->find();
		}
		
		return $cursor;			
	}
		
	//Function responsible for updating the document in a collecction
	function updation($tableName,$selection,$document)
	{
		try { 
			$collection = dbConnection($tableName);
			$update = array('$set' => $document);
			$collection->update($selection, $update);
		}
		catch(MongoConnectionException $e)
		{
			die("Failed to connect to database ".$e->getMessage());
		}		
	}
	
	//Function responsible for deletion of document from the collection
	function deletion($tableName,$document)
	{
		try { 
			$collection = dbConnection($tableName);
			$collection->remove($document);
		}
		catch(MongoConnectionException $e)
		{
			die("Failed to connect to database ".$e->getMessage());
		}		
	}
		
	//Function responsible for couting the documents in the collection
	function countRecords($tableName)
	{
		$cursor = 0;
		try { 
			$collection = dbConnection($tableName);
			$collection = $database->selectCollection($tableName);
		}
		catch(MongoConnectionException $e)
		{
			die("Failed to connect to database ".$e->getMessage());
		}
		
		$cursor = $collection->count();
		
		return $cursor;			
	}
?>
