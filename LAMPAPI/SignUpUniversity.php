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
   $uid = $inData["uid"];
   $access = $inData["access"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
       $stmt2 = $conn->prepare("INSERT INTO `students` (`pid`, `uid`) VALUES (?,?)");
        $stmt2->bind_param("ii", $pid, $uid);
        $stmt2->execute();
        $stmt2->close();
        if ($access == 'admin') {
            // Check if the pid already exists in the admin table
            $checkStmt = $conn->prepare("SELECT pid FROM admin WHERE pid = ?");
            $checkStmt->bind_param("i", $pid);
            $checkStmt->execute();
            $checkStmt->store_result();
        
            if ($checkStmt->num_rows == 0) {
                // If pid doesn't exist, insert into the admin table
                $insertStmt = $conn->prepare("INSERT INTO `admin` (`pid`) VALUES (?)");
                $insertStmt->bind_param("i", $pid);
                $insertStmt->execute();
                $insertStmt->close();
            }
            $checkStmt->close();
            returnWithInfo($uid);
        } else {
            returnWithInfo(0);
        }
       $stmt2->close();
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
    function returnWithInfo(  $id )
	{
        $retValue = '{"id":' . $id . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>