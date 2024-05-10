<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;
	$pid = $inData["pid"];

	$conn = new mysqli("localhost", "root", "your_password_here","users");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("SELECT rid from rso_members where pid= ?");
		$stmt->bind_param("i", $pid);
		$stmt->execute();
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			$stmt2 = $conn->prepare("SELECT * from rso where rid= ?");
			$stmt2->bind_param("i", $row["rid"]);
			$stmt2->execute();
			$result2 = $stmt2->get_result();
			while($row2 = $result2->fetch_assoc())
			{
				if( $searchCount > 0 )
				{
					$searchResults .= ",";
				}
				$searchCount++;
				$searchResults .=json_encode($row2);
			}
			$stmt2->close();
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
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
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>