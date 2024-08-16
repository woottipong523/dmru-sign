<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการเข้ารหัสแบบสมมาตร (Symmetric Encryption)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #555;
        }

        p {
            margin: 10px 0;
        }

        code {
            background-color: #eaeaea;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: "Courier New", Courier, monospace;
        }
    </style>
</head>

<body>
    <h1>คู่มือการเข้ารหัสแบบสมมาตร (Symmetric Encryption)</h1>
    <pre><code>
&lt;?php
function encryptData($data, $privateKey)
{
    $method = 'aes-256-cbc'; // เลือกวิธีการเข้ารหัสที่ต้องการ
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);

    $encryptedData = openssl_encrypt($data, $method, $privateKey, 0, $iv);

    // เพิ่ม IV เข้าไปในข้อมูลที่ถูกเข้ารหัส (เพื่อใช้ในการถอดรหัส)
    $encryptedDataWithIV = $iv . $encryptedData;

    return base64_encode($encryptedDataWithIV);
}

function decryptData($encryptedData, $privateKey)
{
    $method = 'aes-256-cbc'; // เลือกวิธีการเข้ารหัสที่ต้องการ
    $ivLength = openssl_cipher_iv_length($method);

    // ดึง IV ออกจากข้อมูลที่ถูกเข้ารหัส
    $iv = substr(base64_decode($encryptedData), 0, $ivLength);
    $dataWithoutIV = substr(base64_decode($encryptedData), $ivLength);

    $decryptedData = openssl_decrypt($dataWithoutIV, $method, $privateKey, 0, $iv);

    return $decryptedData;
}

$privateKey = "123456";
$data = "WOOTTIPON KONGSIB";
$encrypt = encryptData($data, $privateKey);
$decrypt = decryptData($encrypt, $privateKey);

echo "encrypt: $encrypt";
echo "&lt;br/&gt;";
echo "decrypt: $decrypt";
?&gt;
    </code></pre>
    <h2>การเข้ารหัส (Encryption)</h2>
    <p>เป็นกระบวนการที่ข้อมูลต้นฉบับ (<code>$data</code>) ถูกแปลงเป็นข้อมูลที่ไม่สามารถอ่านได้
        (<code>$encryptedData</code>) โดยใช้กุญแจเข้ารหัส (<code>$privateKey</code>) และตัวเริ่มต้น (Initialization
        Vector หรือ IV)</p>

    <h2>การถอดรหัส (Decryption)</h2>
    <p>เป็นกระบวนการแปลงข้อมูลที่ถูกเข้ารหัส (<code>$encryptedData</code>) กลับมาเป็นข้อมูลต้นฉบับ
        (<code>$decryptedData</code>) โดยใช้กุญแจเข้ารหัสเดียวกันและ IV ที่ใช้ในการเข้ารหัส</p>

    <h2>การทำงานของฟังก์ชัน</h2>
    <ul>
        <li><strong>ฟังก์ชัน <code>encryptData()</code></strong>: ใช้ในการเข้ารหัสข้อมูลด้วย <code>AES-256-CBC</code>
            โดยการใช้กุญแจ (<code>$privateKey</code>) และ IV ที่ถูกสร้างขึ้นแบบสุ่ม</li>
        <li><strong>ฟังก์ชัน <code>decryptData()</code></strong>: ใช้ในการถอดรหัสข้อมูลโดยใช้กุญแจและ IV
            เดิมที่ใช้ในการเข้ารหัส</li>
    </ul>

    <h2>คำจำกัดความที่สำคัญ</h2>
    <ul>
        <li><strong>Encryption (การเข้ารหัส)</strong>: เปลี่ยนข้อมูลให้อยู่ในรูปแบบที่ไม่สามารถอ่านได้
            โดยใช้กุญแจเพื่อเข้ารหัส สามารถถอดรหัสกลับเป็นข้อมูลต้นฉบับได้</li>
        <li><strong>Decryption (การถอดรหัส)</strong>: เปลี่ยนข้อมูลที่ถูกเข้ารหัสกลับเป็นข้อมูลต้นฉบับ
            โดยใช้กุญแจที่ใช้ในการเข้ารหัส</li>
        <li><strong>Symmetric Encryption</strong>: การเข้ารหัสที่ใช้กุญแจเดียวกันในการเข้ารหัสและถอดรหัส</li>
    </ul>

    <h2>เหตุผลที่ผลลัพธ์การเข้ารหัสแต่ละครั้งไม่เหมือนกัน</h2>
    <h3>Initialization Vector (IV)</h3>
    <ul>
        <li>IV เป็นข้อมูลที่ใช้ร่วมกับกุญแจเข้ารหัส (<code>$privateKey</code>) เพื่อเริ่มต้นกระบวนการเข้ารหัส</li>
        <li>IV ถูกสร้างขึ้นมาแบบสุ่มทุกครั้งที่ทำการเข้ารหัสข้อมูลใหม่ โดยใช้ฟังก์ชัน
            <code>openssl_random_pseudo_bytes($ivLength)</code>
        </li>
        <li>การใช้ IV ช่วยให้การเข้ารหัสแต่ละครั้งให้ผลลัพธ์ที่แตกต่างกัน ถึงแม้ว่าจะใช้ข้อมูลและกุญแจเดียวกัน
            นี่เป็นการเพิ่มความปลอดภัยในการเข้ารหัสข้อมูล</li>
    </ul>

    <h3>ความปลอดภัยในการเข้ารหัส</h3>
    <ul>
        <li>การใช้ IV ช่วยป้องกันการโจมตีที่อาจเกิดขึ้น เช่น
            การโจมตีที่พยายามหาข้อมูลต้นฉบับจากผลลัพธ์ของการเข้ารหัสเดิม</li>
        <li>การใช้ IV แบบสุ่มทำให้ผู้โจมตีไม่สามารถเดาได้ว่าข้อมูลที่ถูกเข้ารหัสแต่ละครั้งมีความเกี่ยวข้องกันอย่างไร
        </li>
    </ul>

    <h2>ทำไมถึงสามารถถอดรหัสออกมาได้เหมือนกัน</h2>
    <ul>
        <li>เมื่อคุณทำการถอดรหัส (<code>decryptData</code>), ฟังก์ชันจะดึง IV
            ที่ใช้ในการเข้ารหัสออกจากข้อมูลที่ถูกเข้ารหัส (<code>$encryptedDataWithIV</code>)</li>
        <li>จากนั้นใช้ IV เดิมนี้ร่วมกับกุญแจ (<code>$privateKey</code>) เพื่อถอดรหัสข้อมูลกลับมาเป็นข้อมูลต้นฉบับ
            (<code>$data</code>)</li>
        <li>ดังนั้นถึงแม้ผลลัพธ์การเข้ารหัสแต่ละครั้งจะไม่เหมือนกัน
            แต่การถอดรหัสสามารถทำให้ได้ข้อมูลต้นฉบับเหมือนกันทุกครั้ง</li>
    </ul>

</body>

</html>