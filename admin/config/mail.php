<?php

// ============================================================
// Configuración de correo – PHPMailer
// Sprint 06: Implementación de librería de envío de correos
// ============================================================

// --- Servidor SMTP ---
define('MAIL_HOST',       'smtp.gmail.com');  // Cambia por tu servidor SMTP
define('MAIL_PORT',       587);               // 587 = TLS, 465 = SSL
define('MAIL_ENCRYPTION', 'tls');             // 'tls' o 'ssl'

// --- Credenciales ---
// Para Gmail: usa una "Contraseña de aplicación" (no tu contraseña normal)
// https://myaccount.google.com/apppasswords
define('MAIL_USERNAME',   'victorml7707@gmail.com');
define('MAIL_PASSWORD',   'jjng myyj vtmp pmzy');

// --- Remitente ---
define('MAIL_FROM_EMAIL', 'victorml7707@gmail.com');
define('MAIL_FROM_NAME',  'MediCitas Sistema');

// --- Opciones adicionales ---
define('MAIL_DEBUG',      0);  // 0=Sin debug | 1=Cliente | 2=Cliente+Servidor
