<?php //SignUp.php
    //code to show PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
   $inData = getRequestInfo();
    $pid = $inData["pid"];
    $cid = $inData["cid"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
        $stmt = $conn->prepare("DELETE FROM comments WHERE pid=? AND cid=?");
        $stmt->bind_param("ii", $pid, $cid);
        $stmt->execute();
        $result = $stmt->get_result();
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