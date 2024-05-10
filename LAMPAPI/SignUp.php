<?php //SignUp.php
    //code to show PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
   $inData = getRequestInfo();
   
   $id = 1;
   $login = $inData["login"];
   $firstName = $inData["firstName"];
   $lastName = $inData["lastName"];
   $password = $inData["password"];
   $email = $inData["email"];
    $phone = $inData["phone"];
    $access = $inData["access"];

   //connecting to the database
   $conn = new mysqli("localhost", "root", "your_password_here","users");
   if( $conn->connect_error )
   {
       returnWithError( $conn->connect_error );
   }
   else
   {
       $stmt = $conn->prepare("SELECT pid, username FROM user WHERE username=?");
       $stmt->bind_param("s", $inData["login"]);
       $stmt->execute();
       $result = $stmt->get_result();

       if( $row = $result->fetch_assoc() )
       {
            returnWithInfo( $inData['firstName'], $inData['lastName'], $row['pid'] ); 
       }
       else
       {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO `user` (`username`, `password`, `first_name`, `last_name`, `phone`, `email`, `access`) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss", $login, $password, $firstName, $lastName, $email, $phone, $access);// new edit
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("SELECT pid FROM user WHERE username=?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $id = $row['pid'];
            if($access == 'super admin'){
                $stmt2 = $conn->prepare("INSERT INTO `super_admin` (`pid`) VALUES (?)");
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
            }

            returnWithInfo( $inData['firstName'], $inData['lastName'], -1*($id));
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
		$retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
	}

    // added returnWithInfo with 5 values: $firstName, $lastName, $login, $password, $id
    function returnWithInfo( $firstName, $lastName, $id )
	{
        $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>