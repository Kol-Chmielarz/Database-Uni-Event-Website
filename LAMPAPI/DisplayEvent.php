<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
	$inData = getRequestInfo();
	
$searchResults = "";
$searchCount = 0;

$conn = new mysqli("localhost", "root", "your_password_here", "users");
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    $category = $inData["category"];
    $pid = $inData["pid"];
    $uid = $inData["uid"];

    if ($category == "rso") {
        $stmt2 = $conn->prepare("SELECT rid from rso_members where pid = ?");
        $stmt2->bind_param("i", $pid);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        while ($row2 = $result2->fetch_assoc()) {
            $stmt3 = $conn->prepare("SELECT * from events where category = ?");
            $stmt3->bind_param("i", $row2['rid']); // Use the correct index to access the value
            $stmt3->execute();
            $result3 = $stmt3->get_result();

            while ($row3 = $result3->fetch_assoc()) {
                if ($searchCount > 0) {
                    $searchResults .= ",";
                }
                $searchCount++;
                $searchResults .= json_encode($row3);
            }
        }
    } else if ($category == "private") {
        $stmt = $conn->prepare("SELECT * from events where category = 'private' and uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * from events where category = 'public'");
        $stmt->execute();
    }

    if (isset($stmt)) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($searchCount > 0) {
                $searchResults .= ",";
            }
            $searchCount++;
            $searchResults .= json_encode($row);
        }
    }

    if ($searchCount == 0) {
        returnWithError("No Records Found");
    } else {
        returnWithInfo($searchResults);
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