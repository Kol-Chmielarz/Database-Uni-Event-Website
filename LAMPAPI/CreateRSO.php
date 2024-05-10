<?php //SignUp.php
    //code to show PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
   $inData = getRequestInfo();
   
   $aid = 1;
   $name = $inData["name"];
   $descripton = " " . $inData["description"];
   $descripton = mb_substr($descripton, 1);
    $pid = $inData["pid"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
        $stmt = $conn->prepare("SELECT aid FROM admin WHERE pid=?");
       $stmt->bind_param("s", $pid);
       $stmt->execute();
       $result = $stmt->get_result();
       $row = $result->fetch_assoc();
       $aid = $row['aid'];
        $stmt->close();
       $stmt = $conn->prepare("SELECT rid FROM rso WHERE name=?");
       $stmt->bind_param("s", $name);
       $stmt->execute();
       $result = $stmt->get_result();

       if( $row = $result->fetch_assoc() )
       {
            returnWithInfo( $row['rid'] ); 
       }
       else
       {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO `rso` (`aid`,`name`, `description`) VALUES (?,?,?)");
            $stmt->bind_param("iss", $aid, $name, $descripton,);// new edit
            $stmt->execute();
            $stmt->close();
            returnWithInfo(0);
       }
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
        $retValue = '{"id":"' . $id  . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>