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
   $rid = $inData["rid"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
    $stmt = $conn->prepare("SELECT rid FROM rso_members WHERE pid=? and rid = ?");
    $stmt->bind_param("ii", $pid, $rid);
    $stmt->execute();
    $result = $stmt->get_result();
        if( $row = $result->fetch_assoc() )
        {
            returnWithInfo( $row['id'] ); 
        }
        else
        {
         $stmt->close();
       $stmt2 = $conn->prepare("INSERT INTO `rso_members` (`pid`, `rid`) VALUES (?,?)");
        $stmt2->bind_param("ii", $pid, $rid);
        $stmt2->execute();
        returnWithInfo(0);
       $stmt2->close();
       $conn->close(); 
    }
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
    function returnWithInfo(  $id )
	{
        $retValue = '{"id":' . $id . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>