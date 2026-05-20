<?php

// ============================================================
// Servicio de correo – PHPMailer
// Sprint 06: Librería de envío de correos
// ============================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/mail.php';

class MailService {

    /**
     * Crea y configura una instancia de PHPMailer lista para enviar.
     */
    private static function crearMailer(): PHPMailer {
        $mail = new PHPMailer(true); // true = lanza excepciones

        // Servidor SMTP
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPDebug  = MAIL_DEBUG;

        // Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

        return $mail;
    }

    // ----------------------------------------------------------
    // 1. Correo de bienvenida al crear un usuario
    // ----------------------------------------------------------
    public static function bienvenidaUsuario(string $destinatario, string $nombre, string $password): bool {
        try {
            $mail = self::crearMailer();

            $mail->addAddress($destinatario, $nombre);
            $mail->isHTML(true);
            $mail->Subject = 'Bienvenido a MediCitas – Tu cuenta ha sido creada';
            $mail->Body    = self::templateBienvenida($nombre, $destinatario, $password);
            $mail->AltBody = "Hola $nombre,\n\nTu cuenta en MediCitas ha sido creada.\n\nCorreo: $destinatario\nContraseña: $password\n\nPor seguridad, cambia tu contraseña al iniciar sesión.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailService] Error al enviar bienvenida: ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // 2. Correo de verificación de cuenta (registro público)
    // ----------------------------------------------------------
    public static function confirmacionRegistro(string $destinatario, string $nombre, string $link): bool {
        try {
            $mail = self::crearMailer();
            $mail->addAddress($destinatario, $nombre);
            $mail->isHTML(true);
            $mail->Subject = 'MediCitas – Confirma tu cuenta';
            $mail->Body    = self::templateConfirmacionRegistro($nombre, $link);
            $mail->AltBody = "Hola $nombre,\n\nConfirma tu cuenta haciendo clic en el siguiente enlace (válido 24 horas):\n$link\n\nSi no creaste esta cuenta, ignora este mensaje.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailService] Error al enviar confirmación de registro: ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // 3. Correo de recuperación de contraseña
    // ----------------------------------------------------------
    public static function recuperarContrasena(string $destinatario, string $nombre, string $link): bool {
        try {
            $mail = self::crearMailer();
            $mail->addAddress($destinatario, $nombre);
            $mail->isHTML(true);
            $mail->Subject = 'MediCitas – Restablece tu contraseña';
            $mail->Body    = self::templateRecuperarContrasena($nombre, $link);
            $mail->AltBody = "Hola $nombre,\n\nRecibimos una solicitud para restablecer tu contraseña.\n\nEnlace (válido 1 hora):\n$link\n\nSi no solicitaste esto, ignora este correo.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailService] Error al enviar recuperación: ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // 4. Correo de confirmación de cita
    // ----------------------------------------------------------
    public static function confirmacionCita(string $destinatario, string $nombre, array $cita): bool {
        try {
            $mail = self::crearMailer();

            $mail->addAddress($destinatario, $nombre);
            $mail->isHTML(true);
            $mail->Subject = 'MediCitas – Confirmación de tu cita médica';
            $mail->Body    = self::templateConfirmacionCita($nombre, $cita);
            $mail->AltBody = "Hola $nombre,\n\nTu cita ha sido agendada.\n\nFecha: {$cita['fecha']}\nHora: {$cita['hora']}\nMédico: {$cita['medico']}\n\nGracias por usar MediCitas.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailService] Error al enviar confirmación de cita: ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // TEMPLATES HTML
    // ----------------------------------------------------------

    private static function templateRecuperarContrasena(string $nombre, string $link): string {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                <tr>
                  <td style="background:#005f99;padding:30px 40px;text-align:center;">
                    <h1 style="color:#ffffff;margin:0;font-size:26px;">🏥 MediCitas</h1>
                    <p style="color:#b3d9f2;margin:8px 0 0;">Recuperación de contraseña</p>
                  </td>
                </tr>
                <tr>
                  <td style="padding:40px;">
                    <h2 style="color:#005f99;margin-top:0;">Hola, {$nombre}</h2>
                    <p style="color:#444;line-height:1.7;">
                      Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>MediCitas</strong>.
                      Haz clic en el botón de abajo. Este enlace es válido por <strong>1 hora</strong>.
                    </p>
                    <div style="text-align:center;margin:32px 0;">
                      <a href="{$link}" style="background:#005f99;color:#ffffff;padding:14px 36px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:16px;">
                        Restablecer contraseña
                      </a>
                    </div>
                    <p style="color:#888;font-size:13px;">
                      Si el botón no funciona, copia y pega este enlace:<br>
                      <a href="{$link}" style="color:#005f99;word-break:break-all;">{$link}</a>
                    </p>
                    <p style="color:#999;font-size:12px;border-top:1px solid #eee;padding-top:16px;margin-top:24px;">
                      Si no solicitaste restablecer tu contraseña, ignora este correo. Tu cuenta está segura.
                    </p>
                  </td>
                </tr>
                <tr>
                  <td style="background:#f7fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="color:#999;font-size:12px;margin:0;">Este correo fue generado automáticamente por MediCitas.</p>
                  </td>
                </tr>
              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private static function templateConfirmacionRegistro(string $nombre, string $link): string {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                <tr>
                  <td style="background:#005f99;padding:30px 40px;text-align:center;">
                    <h1 style="color:#ffffff;margin:0;font-size:26px;">🏥 MediCitas</h1>
                    <p style="color:#b3d9f2;margin:8px 0 0;">Sistema de Gestión de Citas Médicas</p>
                  </td>
                </tr>
                <tr>
                  <td style="padding:40px;">
                    <h2 style="color:#005f99;margin-top:0;">Confirma tu cuenta, {$nombre}</h2>
                    <p style="color:#444;line-height:1.7;">
                      Gracias por registrarte en <strong>MediCitas</strong>. Para activar tu cuenta haz clic en el botón de abajo.
                      Este enlace es válido por <strong>24 horas</strong>.
                    </p>
                    <div style="text-align:center;margin:32px 0;">
                      <a href="{$link}" style="background:#005f99;color:#ffffff;padding:14px 36px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:16px;">
                        Verificar mi cuenta
                      </a>
                    </div>
                    <p style="color:#888;font-size:13px;">
                      Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                      <a href="{$link}" style="color:#005f99;word-break:break-all;">{$link}</a>
                    </p>
                    <p style="color:#999;font-size:12px;border-top:1px solid #eee;padding-top:16px;margin-top:24px;">
                      Si no creaste esta cuenta, ignora este correo.
                    </p>
                  </td>
                </tr>
                <tr>
                  <td style="background:#f7fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="color:#999;font-size:12px;margin:0;">
                      Este correo fue generado automáticamente por MediCitas. Por favor no respondas a este mensaje.
                    </p>
                  </td>
                </tr>
              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private static function templateBienvenida(string $nombre, string $email, string $password): string {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                
                <!-- Header -->
                <tr>
                  <td style="background:#005f99;padding:30px 40px;text-align:center;">
                    <h1 style="color:#ffffff;margin:0;font-size:26px;">🏥 MediCitas</h1>
                    <p style="color:#b3d9f2;margin:8px 0 0;">Sistema de Gestión de Citas Médicas</p>
                  </td>
                </tr>

                <!-- Body -->
                <tr>
                  <td style="padding:40px;">
                    <h2 style="color:#005f99;margin-top:0;">¡Bienvenido, {$nombre}!</h2>
                    <p style="color:#444;line-height:1.7;">
                      Tu cuenta en <strong>MediCitas</strong> ha sido creada exitosamente por el administrador del sistema.
                      A continuación encontrarás tus credenciales de acceso:
                    </p>

                    <!-- Credenciales -->
                    <table width="100%" style="background:#f0f8ff;border-radius:8px;margin:20px 0;">
                      <tr>
                        <td style="padding:20px;">
                          <p style="margin:0 0 10px;color:#666;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Tus credenciales</p>
                          <p style="margin:6px 0;color:#333;"><strong>📧 Correo:</strong> {$email}</p>
                          <p style="margin:6px 0;color:#333;"><strong>🔑 Contraseña:</strong> <code style="background:#dce8f5;padding:2px 8px;border-radius:4px;font-size:15px;">{$password}</code></p>
                        </td>
                      </tr>
                    </table>

                    <p style="color:#e53e3e;font-size:13px;background:#fff5f5;border-left:4px solid #e53e3e;padding:10px 15px;border-radius:4px;">
                      ⚠️ <strong>Por seguridad</strong>, te recomendamos cambiar tu contraseña al iniciar sesión por primera vez.
                    </p>

                    <div style="text-align:center;margin:30px 0;">
                      <a href="#" style="background:#005f99;color:#ffffff;padding:12px 30px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:15px;">
                        Iniciar sesión →
                      </a>
                    </div>
                  </td>
                </tr>

                <!-- Footer -->
                <tr>
                  <td style="background:#f7fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="color:#999;font-size:12px;margin:0;">
                      Este correo fue generado automáticamente por MediCitas. Por favor no respondas a este mensaje.
                    </p>
                  </td>
                </tr>

              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private static function templateConfirmacionCita(string $nombre, array $cita): string {
        $fecha  = htmlspecialchars($cita['fecha']  ?? 'N/A');
        $hora   = htmlspecialchars($cita['hora']   ?? 'N/A');
        $medico = htmlspecialchars($cita['medico'] ?? 'N/A');
        $motivo = htmlspecialchars($cita['motivo'] ?? 'N/A');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                
                <tr>
                  <td style="background:#005f99;padding:30px 40px;text-align:center;">
                    <h1 style="color:#ffffff;margin:0;font-size:26px;">🏥 MediCitas</h1>
                    <p style="color:#b3d9f2;margin:8px 0 0;">Confirmación de Cita Médica</p>
                  </td>
                </tr>

                <tr>
                  <td style="padding:40px;">
                    <h2 style="color:#005f99;margin-top:0;">Tu cita ha sido confirmada ✅</h2>
                    <p style="color:#444;line-height:1.7;">Hola <strong>{$nombre}</strong>, tu cita médica ha sido agendada con los siguientes detalles:</p>

                    <table width="100%" style="background:#f0f8ff;border-radius:8px;margin:20px 0;">
                      <tr><td style="padding:20px;">
                        <p style="margin:8px 0;color:#333;"><strong>📅 Fecha:</strong> {$fecha}</p>
                        <p style="margin:8px 0;color:#333;"><strong>🕐 Hora:</strong> {$hora}</p>
                        <p style="margin:8px 0;color:#333;"><strong>👨‍⚕️ Médico:</strong> Dr. {$medico}</p>
                        <p style="margin:8px 0;color:#333;"><strong>📋 Motivo:</strong> {$motivo}</p>
                      </td></tr>
                    </table>

                    <p style="color:#444;font-size:14px;">Te pedimos llegar 10 minutos antes de tu cita. Si necesitas cancelar, comunícate con nosotros con anticipación.</p>
                  </td>
                </tr>

                <tr>
                  <td style="background:#f7fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="color:#999;font-size:12px;margin:0;">
                      Este correo fue generado automáticamente por MediCitas.
                    </p>
                  </td>
                </tr>

              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }
}
