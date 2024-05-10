<?php //SignUp.php
    //code to show PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
   $inData = getRequestInfo();
   
   $name = $inData["name"];
   $comment = $inData["comment"];
   $rating = $inData["rating"];
    $pid = $inData["pid"];
    $eid = $inData["eid"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
        $stmt = $conn->prepare("INSERT INTO `comments` (`eid`, `pid`, `comments`, `rating`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $eid, $pid, $comment, $rating);
        $stmt->execute();
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
		$retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
	}

    // added returnWithInfo with 5 values: $firstName, $lastName, $login, $password, $id
    function returnWithInfo( $id )
	{
        $retValue = '{"id":' . $id  . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>