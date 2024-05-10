<?php //SignUp.php
    //code to show PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
    function getRequestInfo() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    // Function to send JSON response
    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo json_encode($obj);
    }
    
    // Function to return error response
    function returnWithError($err) {
        $retValue = array("error" => $err);
        sendResultInfoAsJson($retValue);
        exit();
    }
    
    // Function to return success response
    function returnWithInfo($id) {
        $retValue = array("id" => $id, "error" => "");
        sendResultInfoAsJson($retValue);
        exit();
    }
    
    // Get request data
    $inData = getRequestInfo();
    
    // Extract request parameters
    $name = $inData["name"];
    $description = $inData["description"];
    $category = $inData["category"];
    $time = $inData["time"];
    $location = $inData["location"];
    $email = $inData["email"];
    $phone = $inData["phone"];
    $pid = $inData["pid"];
    
    // Connect to database
    $conn = new mysqli("localhost", "root", "your_password_here", "users");
    if ($conn->connect_error) {
        returnWithError($conn->connect_error);
    }
    
    // Prepare and execute SQL queries
    $stmt = $conn->prepare("SELECT aid FROM admin WHERE pid=?");
    $stmt->bind_param("s", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        returnWithError("Admin not found for provided PID.");
    }
    
    $row = $result->fetch_assoc();
    $aid = $row['aid'];
    $stmt->close();
    
    $stmt = $conn->prepare("SELECT uid FROM students WHERE pid=?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        returnWithError("User not found for provided PID.");
    }
    
    $row = $result->fetch_assoc();
    $uid = $row['uid'];
    $stmt->close();
    
    $stmt = $conn->prepare("INSERT INTO events (name, location, time, category, description, email, phone, uid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $location, $time, $category, $description, $email, $phone, $uid);
    
    if (!$stmt->execute()) {
        returnWithError("Failed to create event.");
    }
    
    $stmt->close();
    
    $stmt = $conn->prepare("SELECT eid FROM events WHERE name=? AND description=? AND email=? AND phone=?");
    $stmt->bind_param("ssss", $name, $description, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        returnWithError("Failed to retrieve event ID.");
    }
    
    $row = $result->fetch_assoc();
    $eid = $row['eid'];
    $stmt->close();
    
    // Insert event ID into appropriate category-specific table
    if ($category == "public") {
        $stmt = $conn->prepare("INSERT INTO public_events (eid, aid) VALUES (?, ?)");
        $stmt->bind_param("ii", $eid, $aid);
    } elseif ($category == "private") {
        $stmt = $conn->prepare("INSERT INTO private_events (eid, aid, uid) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $eid, $aid, $uid);
    } else {
        $stmt = $conn->prepare("SELECT rid, status FROM rso WHERE rid=?");
        $stmt->bind_param("i", $category);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            returnWithError("RSO not found for provided category.");
        }
    
        $row = $result->fetch_assoc();
        $rid = $row['rid'];
        $active = $row['status'];
        $stmt->close();
    
        $stmt = $conn->prepare("INSERT INTO rso_events (eid, rid) VALUES (?, ?)");
        $stmt->bind_param("ii", $eid, $rid);
    }
    
    if (!$stmt->execute()) {
        returnWithError("Failed to insert event ID into category-specific table.");
    }
    
    $stmt->close();
    $conn->close();
    
    returnWithInfo($eid);
    ?>