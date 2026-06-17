<?php
// public/enviar.php

header("Access-Control-Allow-Origin: https://horecabl.com.mx");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nombre = filter_var($input['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $empresa = filter_var($input['empresa'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $telefono = filter_var($input['telefono'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $correo = filter_var($input['correo'] ?? '', FILTER_VALIDATE_EMAIL);
    $mensaje = filter_var($input['mensaje'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    
    $servicios = $input['servicios'] ?? [];
    $serviciosTexto = "Ninguno seleccionado";
    if (is_array($servicios) && !empty($servicios)) {
        $serviciosSanitizados = array_map(function($s) {
            return filter_var($s, FILTER_SANITIZE_SPECIAL_CHARS);
        }, $servicios);
        $serviciosTexto = implode(", ", $serviziossanitizados);
    }

    if (!$nombre || !$correo || !$mensaje) {
        echo json_encode(["status" => "error", "message" => "Por favor llena los campos obligatorios (Nombre, Correo y Mensaje)."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.horecabl.com.mx';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noehbl@horecabl.com.mx'; 
        $mail->Password   = 'rCyOJ6*c&R&bhlLh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;                        

        $mail->setFrom('noehbl@horecabl.com.mx', 'Pruebas Web');
        $mail->addAddress('noehbl@horecabl.com.mx'); 
        $mail->addReplyTo($correo, $nombre);         

        $mail->isHTML(true);
        $mail->Subject = "[TEST] Lead - $nombre";
        
        $mail->Body    = "
            <h2>Nuevo mensaje de cliente interesado (Entorno Pruebas)</h2>
            <p><strong>Nombre completo:</strong> {$nombre}</p>
            <p><strong>Empresa:</strong> " . ($empresa ? $empresa : 'No especificada') . "</p>
            <p><strong>Teléfono:</strong> " . ($telefono ? $telefono : 'No proporcionado') . "</p>
            <p><strong>Correo electrónico:</strong> {$correo}</p>
            <p><strong>Servicios requeridos:</strong> {$serviciosTexto}</p>
            <p><strong>Mensaje o Proyecto:</strong><br>" . nl2br($mensaje) . "</p>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Tu información ha sido enviada con éxito."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "El servidor de correos falló. Detalles: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
}