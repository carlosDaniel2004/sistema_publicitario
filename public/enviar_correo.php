<?php
session_start();
// ... (código de protección y obtención de datos) ...

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

// ... (lógica para procesar el formulario) ...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenido_id'])) {
    
    // ... (obtener correos de estudiantes y contenido del anuncio) ...

    $mail = new PHPMailer(true);
    try {
        //Configuración del Servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // O el de tu correo institucional
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu_correo@gmail.com'; // Tu correo
        $mail->Password   = 'tu_contrasena_de_aplicacion'; // ¡IMPORTANTE! Usa una contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Remitente y destinatarios
        $mail->setFrom('tu_correo@gmail.com', 'Escuela de Sistemas');
        
        // Agregar todos los correos en copia oculta (BCC) para proteger la privacidad
        foreach ($estudiantes as $estudiante) {
            $mail->addBCC($estudiante['correo_institucional']);
        }

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;
        $mail->AltBody = strip_tags($cuerpo_html);

        $mail->send();
        $mensaje_exito = '¡El correo ha sido enviado a todos los estudiantes!';
    } catch (Exception $e) {
        $mensaje_error = "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }
}
// ... (resto del HTML del formulario) ...
?>