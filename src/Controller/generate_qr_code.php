<?php
use Endroid\QrCode\QrCode;

// Generate the QR code
$qrCode = new QrCode($reservationData);
$qrCode->setSize(300);
$qrCode->setMargin(10);
$qrCode->setWriterByName('png');
$qrCode->setEncoding('UTF-8');

// Save the QR code as an image file
$qrCodePath = 'path/to/qrcode.png';
$qrCode->writeFile($qrCodePath);
