<?php
header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// กำหนด Secret key ที่ใช้สำหรับตรวจสอบ JWT token
$secretKey = 'your_secret_key';

// รับ JWT token จาก Header
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwtToken = $matches[1];

    try {
        $decoded = JWT::decode($jwtToken, new Key($secretKey, 'HS256'));
        $decoded_array = (array) $decoded;
        echo json_encode([
            "status" => "success",
            "message" => "Token and data are valid",
            "received_data" => $decoded_array
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid token: " . $e->getMessage()
        ]);
    }
} else {
    // ถ้าไม่ได้ส่ง JWT token มา
    echo json_encode([
        "status" => "error",
        "message" => "Authorization header missing or invalid"
    ]);
}
?>