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
$eid = $inData["eid"];

// Connect to the database
$conn = new mysqli("localhost", "root", "your_password_here", "users");
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Check if the user has already joined the event
    $stmt = $conn->prepare("SELECT eid FROM event_member WHERE pid=?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row["eid"] == $eid) {
            returnWithError("Already joined event");
            exit(); // Stop further execution
        }

        // Check for time conflict
        $stmt2 = $conn->prepare("SELECT time FROM events WHERE eid=?");
        $stmt2->bind_param("i", $row["eid"]);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();

        $stmt3 = $conn->prepare("SELECT time FROM events WHERE eid=?");
        $stmt3->bind_param("i", $eid);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        $row3 = $result3->fetch_assoc();

        if ($row2["time"] == $row3["time"]) {
            returnWithError("Time conflict");
            exit(); // Stop further execution
        }
    }

    // Insert the user into the event_member table
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO event_member (eid, pid) VALUES (?, ?)");
    $stmt->bind_param("ii", $eid, $pid);
    $stmt->execute();
    returnWithError("Joined Successfully");

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
        $retValue = '{"id":"' . $id  . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>