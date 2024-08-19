<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// กำหนด Secret key สำหรับการลงนามใน Token
$secretKey = 'your_secret_key';

// ข้อมูลที่ต้องการใส่ใน Payload
$payload = [
    'iss' => 'your-website.com',  // Issuer: ที่มาของ Token
    'aud' => 'your-website.com',  // Audience: ผู้รับ Token
    'iat' => time(),              // Issued at: เวลาที่ออก Token
    'nbf' => time(),              // Not before: เวลาที่เริ่มใช้ Token ได้
    'exp' => time() + 3600,       // Expiration time: เวลาหมดอายุของ Token (1 ชั่วโมงจากเวลาปัจจุบัน)
    'user_id' => 123,             // ข้อมูลเพิ่มเติม เช่น ID ของผู้ใช้
];

// การเข้ารหัส Token
$jwt = JWT::encode($payload, $secretKey, 'HS256');

echo "Generated JWT: " . $jwt;
