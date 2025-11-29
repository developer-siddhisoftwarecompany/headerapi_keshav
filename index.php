<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Load stored numbers
$dataFile = "data.json";
$storedNumbers = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Get all headers
$headers = $headers = array_change_key_case($_SERVER, CASE_LOWER);


// API 1: AUTH
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($headers["http_auth_key"])
) {
    $correctKey = "12345";
    if ($headers["auth-key"] === $correctKey) {
        echo json_encode(["status" => "success", "message" => "Welcome!"]);
    } else {
        echo json_encode(["status" => "failed", "message" => "Retry with different number"]);
    }
    exit;
}

// API 2: SAVE NUMBER
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($headers["save-number"])) {
    $numberToSave = $headers["save-number"];
    if (!in_array($numberToSave, $storedNumbers)) {
        $storedNumbers[] = $numberToSave;
        file_put_contents($dataFile, json_encode($storedNumbers, JSON_PRETTY_PRINT));
    }
    echo json_encode(["message" => "Number saved successfully"]);
    exit;
}

// API 3: CHECK NUMBER
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($headers["check-number"])) {
    $num = $headers["check-number"];
    if (in_array($num, $storedNumbers)) {
        echo json_encode([
            "exists" => true,
            "message" => "Number is stored on the server"
        ]);
    } else {
        echo json_encode([
            "exists" => false,
            "message" => "Number not found"
        ]);
    }
    exit;
}

echo json_encode(["error" => "Invalid request or missing headers"]);
?>
