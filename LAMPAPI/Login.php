<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
	$inData = getRequestInfo();
	
	$pid = 0;
	$first_name = "";
	$last_name = "";
	$uid = 0;

	$conn = new mysqli("localhost", "root", "your_password_here","users"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT pid,first_name,last_name,email,phone,access FROM user WHERE username =? AND password =?");
		$stmt->bind_param("ss", $inData["username"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			if($row['access'] != "super admin" ){
				$stmt2 = $conn->prepare("SELECT uid FROM students WHERE pid =? ");
				$stmt2->bind_param("s", $row['pid']);
				$stmt2->execute();
				$result2 = $stmt2->get_result();
				
				if($row2 = $result2->fetch_assoc()){
					returnWithInfo( $row['first_name'], $row['last_name'], $row['pid'], $row['access'], $row['email'] , $row['phone'], $row2['uid']);
				}
				else{
					returnWithInfo( $row['first_name'], $row['last_name'], $row['pid'], $row['access'], $row['email'] , $row['phone'], 0);
				}
			}
			else{
				returnWithInfo( $row['first_name'], $row['last_name'], $row['pid'], $row['access'], $row['email'] , $row['phone'], 0);
			}
			
		}
		else
		{
			returnWithError("Wrong username or password");
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
		$retValue = '{"pid":0,"first_name":"","last_name":"","access":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $first_name, $last_name, $pid , $access, $email, $phone , $uid)
	{
		$retValue = '{"pid":' . $pid . ',"first_name":"' . $first_name. '","last_name":"' . $last_name . '","email":"' . $email . '","uid":"' . $uid .'","phone":"' . $phone . '","access":"' . $access . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>