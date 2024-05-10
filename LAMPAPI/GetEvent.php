<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
	$inData = getRequestInfo();
	$conn = new mysqli("localhost", "root", "your_password_here","users");
	$dat = $inData["eid"];
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("SELECT * from events where eid = ?");
		$stmt->bind_param("i", $dat);
		$stmt->execute();
		$result = $stmt->get_result();
		
		
		if($row = $result->fetch_assoc())
		{
			
			returnWithInfo( $row['name'], $row['location'], $row['time'], $row['category'], $row['description'] , $row['phone'], $row['email']);
		}
		else
		{
			returnWithError( "No Records Found" );
		}
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $name, $location, $time , $category, $description, $phone , $email)
	{
		$retValue = '{"name":"' . $name . '","location":"' . $location. '","time":"' . $time . '","email":"' . $email . '","category":"' . $category .'","phone":"' . $phone . '","description":"' . $description . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>