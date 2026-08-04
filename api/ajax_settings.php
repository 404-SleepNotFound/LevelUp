<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);

if (!$request && !empty($_POST)) {
    $request = $_POST;
}

$action = $request['action'] ?? '';

// Mocking the Backend logic
if ($action === 'update_profile') {
    // Validate inputs, update DB...
    echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
    exit;
}

if ($action === 'update_password') {
    // Verify old pass, hash new pass, update DB...
    echo json_encode(["status" => "success", "message" => "Password changed securely."]);
    exit;
}

if ($action === 'toggle_setting') {
    $setting = $request['setting'] ?? '';
    $state = $request['state'] ?? false;
    // Update boolean setting in user preferences JSON/DB column
    echo json_encode(["status" => "success", "message" => "Preference saved."]);
    exit;
}

if ($action === 'update_persona') {
    $persona = $request['persona'] ?? '';
    // Update AI persona config in DB
    echo json_encode(["status" => "success", "message" => "AI Persona updated to: " . ucfirst(str_replace('_', ' ', $persona))]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
?>