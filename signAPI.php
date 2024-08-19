<?php
// เปิดการแสดงข้อผิดพลาดเพื่อการดีบัก
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// รับค่าจากฟอร์ม
$signatureX = isset($_POST['signatureX']) ? (float) $_POST['signatureX'] : 0;
$signatureY = isset($_POST['signatureY']) ? (float) $_POST['signatureY'] : 0;
$signatureWidth = isset($_POST['signatureWidth']) ? (float) $_POST['signatureWidth'] : 120;
$signatureHeight = isset($_POST['signatureHeight']) ? (float) $_POST['signatureHeight'] : 50;

require_once __DIR__ . '/demos/bootstrap.php'; // ปรับเส้นทางนี้ให้ตรงกับที่คุณเก็บไฟล์ Autoload.php

// กำหนดเส้นทางสำหรับทรัพย์สินต่าง ๆ
$assetsDirectory = __DIR__ . '/assets'; // ปรับเส้นทางนี้ให้ตรงกับที่เก็บไฟล์ PDF, ใบรับรอง และภาพลายเซ็น


$writer = new \SetaPDF_Core_Writer_Http('visible-signature.pdf', true);
// $writer = new SetaPDF_Core_Writer_File('output/result.pdf');

$document = \SetaPDF_Core_Document::loadByFilename(
    $assetsDirectory . '/pdfs/master_PQDR62J2RP99M16TAN1KTFJRKJJBSN.pdf',
    $writer
);
// create a signer instance
$signer = new \SetaPDF_Signer($document);
// add a visible signature field
$field = $signer->addSignatureField(
    \SetaPDF_Signer_SignatureField::DEFAULT_FIELD_NAME,
    1, // ใส่หมายเลขหน้าเอกสารที่ต้องการ
    \SetaPDF_Signer_SignatureField::POSITION_LEFT_TOP,
    ['x' => (float) $signatureY, 'y' => (float) $signatureX * -1], // ตำแหน่งของฟิลด์ลายเซ็น
    $signatureWidth,
    $signatureHeight
);
// and define that you want to use this field
$signer->setSignatureFieldName($field->getQualifiedName());

// now create a signature module
$module = new \SetaPDF_Signer_Signature_Module_Pades();
// pass the path to the certificate
$pkcs12 = [];
$pfxRead = openssl_pkcs12_read(
    file_get_contents($assetsDirectory . '/p12/woottipong.p12'),
    $pkcs12,
    '123456' // รหัสผ่านของ PKCS#12, เปลี่ยนตามที่คุณใช้
);
if ($pfxRead === false) {
    throw new Exception('The PFX file could not be read.');
}
$module->setCertificate($pkcs12['cert']);
$module->setPrivateKey($pkcs12['pkey']);
if (isset($pkcs12['extracerts']) && count($pkcs12['extracerts'])) {
    $module->setExtraCertificates($pkcs12['extracerts']);
}

// ใช้ลายเซ็นที่เขียนด้วยมือจากภาพ PNG
$image = \SetaPDF_Core_Image::getByPath($assetsDirectory . '/images/SXRBGNTVV1A1R4Z8TMSVCFVG9F7JZH.png');
$xObject = $image->toXObject($document);
$appearance = new \SetaPDF_Signer_Signature_Appearance_XObject($xObject);
$signer->setAppearance($appearance);


// เซ็นเอกสาร
try {
    $signer->sign($module);
} catch (\Exception $e) {
    echo 'Error during signing: ' . $e->getMessage();
    exit;
}
