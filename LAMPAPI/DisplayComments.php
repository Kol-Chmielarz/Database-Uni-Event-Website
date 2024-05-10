<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

$inData = getRequestInfo();
$conn = new mysqli("localhost", "root", "your_password_here", "users");
$id = $inData["eid"];
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    $stmt = $conn->prepare("SELECT * FROM comments WHERE eid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $searchResults = array();

    while ($row = $result->fetch_assoc()) {
		$stmt2 = $conn->prepare("SELECT username FROM user WHERE pid = ?");
		$stmt2->bind_param("i", $row["pid"]);
		$stmt2->execute();
		$result2 = $stmt2->get_result();
		
		// Fetch the username
		if ($row2 = $result2->fetch_assoc()) {
			$username = $row2["username"];
		} else {
			// Handle case where username is not found (optional)
			$username = "Unknown"; // Set a default value or handle as needed
		}

		// Add the username to the current row
		$row["username"] = $username;

		// Add the modified row to searchResults
		$searchResults[] = $row;
		$stmt2->close();
    }

    if (empty($searchResults)) {
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

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo json_encode($obj);
}

function returnWithError($err)
{
    $retValue = array(
        "results" => array(),
        "error" => $err
    );
    sendResultInfoAsJson($retValue);
}

function returnWithInfo($searchResults)
{
    $retValue = array(
        "results" => $searchResults,
        "error" => ""
    );
    sendResultInfoAsJson($retValue);
}

?>
