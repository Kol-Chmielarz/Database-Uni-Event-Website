<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
	$inData = getRequestInfo();
	
	$searchResults = array(); // Initialize as an array
	$searchResults2 = array(); // Initialize as an array
	$category = $inData["category"];
	$pid = $inData["pid"];
	$uid = $inData["uid"];

	$conn = new mysqli("localhost", "root", "your_password_here", "users");
	if ($conn->connect_error) {
		returnWithError($conn->connect_error);
	} else {
		if ($category == "rso") {
			$stmt2 = $conn->prepare("SELECT rid from rso_members where pid = ?");
			$stmt2->bind_param("i", $pid);
			if (!$stmt2->execute()) {
				returnWithError("Error executing statement");
			}
			$result2 = $stmt2->get_result();
			$row2 = $result2->fetch_assoc();
			$rid = $row2["rid"];
			$stmt2->close();
		} else if ($category == "private") {
			$stmt = $conn->prepare("SELECT * from events where category = ? and uid = ?");
			$stmt->bind_param("si", $category, $uid);
		} 
	}

	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson($obj) {
		header('Content-type: application/json');
		echo json_encode($obj);
	}
	
	function returnWithError($err) {
		$retValue = array("error" => $err);
		sendResultInfoAsJson($retValue);
	}
	
	function returnWithInfo($searchResults) {
		$retValue = array("results" => $searchResults, "error" => "");
		sendResultInfoAsJson($retValue);
	}
?>
