<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Load stored numbers
$dataFile = "data.json";
$storedNumbers = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Read headers from $_SERVER 
$authKey    = $_SERVER["HTTP_AUTH_KEY"]    ?? null;
$saveNumber = $_SERVER["HTTP_SAVE_NUMBER"] ?? null;
$checkNumber = $_SERVER["HTTP_CHECK_NUMBER"] ?? null;



// API 1 → AUTH
if ($_SERVER["REQUEST_METHOD"] === "POST" && $authKey !== null) {

    if ($authKey === "12345") {
        echo json_encode(["status" => "success", "message" => "Welcome!"]);
    } else {
        echo json_encode(["status" => "failed", "message" => "Retry with different number"]);
    }
    exit;
}



// API 2 → SAVE NUMBER
// --------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && $saveNumber !== null) {

    if (!in_array($saveNumber, $storedNumbers)) {
        $storedNumbers[] = $saveNumber;
        file_put_contents($dataFile, json_encode($storedNumbers, JSON_PRETTY_PRINT));
    }

    echo json_encode(["message" => "Number saved successfully"]);
    exit;
}



// API 3 → CHECK NUMBER
if ($_SERVER["REQUEST_METHOD"] === "GET" && $checkNumber !== null) {

    if (in_array($checkNumber, $storedNumbers)) {
        echo json_encode(["exists" => true, "message" => "Number is stored on the server"]);
    } else {
        echo json_encode(["exists" => false, "message" => "Number not found"]);
    }

    exit;
}


// DEFAULT
echo json_encode(["error" => "Invalid request or missing headers"]);
?>
