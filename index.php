<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

date_default_timezone_set('Asia/Kolkata');

// Load stored numbers
$dataFile = "data.json";
$storedNumbers = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Read headers
$authKey     = $_SERVER["HTTP_AUTH_KEY"] ?? null;
$saveNumber  = $_SERVER["HTTP_SAVE_NUMBER"] ?? null;
$checkNumber = $_SERVER["HTTP_CHECK_NUMBER"] ?? null;

// ----------------------------
// VALIDATION FUNCTION (6 digits only)
// ----------------------------
function validateSixDigitNum($num) {

    // Check numeric
    if (!ctype_digit($num)) {
        return [
            "valid" => false,
            "message" => "Please enter numbers only"
        ];
    }

    // Check exact length
    if (strlen($num) !== 6) {
        return [
            "valid" => false,
            "message" => "Number must be exactly 6 digits"
        ];
    }

    return ["valid" => true];
}




// ----------------------------
// API 1 — AUTH (Fixed 6-digit Key)
// ----------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && $authKey !== null) {

    // Validate must be 6 digits only
    $valid = validateSixDigitNum($authKey);
    if (!$valid["valid"]) {
        echo json_encode($valid);
        exit;
    }

    // Check fixed key
    if ($authKey === "123456") {
        echo json_encode([
            "status" => "success",
            "message" => "Welcome!"
        ]);
    } else {
        echo json_encode([
            "status" => "failed",
            "message" => "Retry with different number"
        ]);
    }

    exit;
}



// ----------------------------
// API 2 — SAVE NUMBER
// ----------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && $saveNumber !== null) {

    // Validate input
    $valid = validateSixDigitNum($saveNumber);
    if (!$valid["valid"]) {
        echo json_encode($valid);
        exit;
    }

    // Save number
    if (!in_array($saveNumber, $storedNumbers)) {
        $storedNumbers[] = $saveNumber;
        file_put_contents("data.json", json_encode($storedNumbers, JSON_PRETTY_PRINT));
    }

    echo json_encode(["message" => "Number saved successfully"]);
    exit;
}


// ----------------------------
// API 3 — CHECK NUMBER
// ----------------------------
if ($_SERVER["REQUEST_METHOD"] === "GET" && $checkNumber !== null) {

    // Validate input
    $valid = validateSixDigitNum($checkNumber);
    if (!$valid["valid"]) {
        echo json_encode($valid);
        exit;
    }

    // Check number existence
    if (in_array($checkNumber, $storedNumbers)) {
        echo json_encode(["exists" => true, "message" => "Number found"]);
    } else {
        echo json_encode(["exists" => false, "message" => "Number not found"]);
    }
    exit;
}


// DEFAULT RESPONSE
echo json_encode(["error" => "Invalid request or missing headers"]);
?>
