<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// กำหนด Secret key สำหรับตรวจสอบ Token
$secretKey = 'your_secret_key';

$jwt = $_GET['token']; // Token ที่ได้รับจากผู้ใช้งาน

try {
    $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
    $decoded_array = (array) $decoded;

    // ถ้า Token ถูกต้อง
    echo "Token ถูกต้อง!";
    print_r($decoded_array);
} catch (Exception $e) {
    // ถ้า Token ไม่ถูกต้อง
    echo "Token ไม่ถูกต้อง: " . $e->getMessage();
}
