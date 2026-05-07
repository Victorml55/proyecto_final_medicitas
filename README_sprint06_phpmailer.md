# Sprint 06 – PHPMailer: Envío de Correos en MediCitas

## ¿Qué se implementó?

Se integró la librería **PHPMailer v6.9** para el envío de correos electrónicos automáticos en dos puntos del sistema:

1. **Correo de bienvenida** → Se envía automáticamente al crear un nuevo usuario.
2. **Confirmación de cita** → Se envía al paciente cuando se agenda una cita.

---

## Archivos nuevos / modificados

| Archivo | Tipo | Descripción |
|---|---|---|
| `composer.json` | Nuevo | Declara PHPMailer y TCPDF como dependencias |
| `Dockerfile` | Modificado | Añade Composer e instala dependencias |
| `admin/config/mail.php` | Nuevo | Configuración SMTP (host, puerto, credenciales) |
| `admin/services/MailService.php` | Nuevo | Clase con métodos para cada tipo de correo |
| `admin/usuario.php` | Modificado | Llama a `MailService::bienvenidaUsuario()` al crear usuario |
| `admin/cita.php` | Modificado | Llama a `MailService::confirmacionCita()` al crear cita |
| `admin/models/cita.php` | Modificado | Agrega métodos helper para obtener datos de correo |

---

## Configuración antes de usar

### 1. Editar `admin/config/mail.php`

```php
define('MAIL_USERNAME', 'tu_correo@gmail.com');
define('MAIL_PASSWORD', 'tu_contrasena_de_aplicacion'); // NO tu contraseña normal
define('MAIL_FROM_EMAIL', 'tu_correo@gmail.com');
```

### 2. Generar contraseña de aplicación en Gmail

1. Ve a [myaccount.google.com](https://myaccount.google.com)
2. Seguridad → Verificación en dos pasos (actívala si no está)
3. Seguridad → Contraseñas de aplicaciones
4. Selecciona "Otra" → ponle nombre "MediCitas" → Copiar la contraseña generada

### 3. Levantar el proyecto

```bash
docker-compose up --build
```

Composer instalará PHPMailer automáticamente durante el build.

---

## ¿Cómo funciona PHPMailer?

```
Admin crea usuario
       │
       ▼
usuario.php (accion=crear)
       │
       ├─► Guarda en BD (password_hash)
       │
       └─► MailService::bienvenidaUsuario()
                   │
                   ▼
           PHPMailer → SMTP Gmail
                   │
                   ▼
           Correo HTML al nuevo usuario
           con sus credenciales de acceso
```

---

## Librería utilizada: PHPMailer

- **Repositorio:** https://github.com/PHPMailer/PHPMailer
- **Versión:** ^6.9
- **Instalación:** `composer require phpmailer/phpmailer`
- **Por qué PHPMailer:**
  - Es el estándar de la industria para PHP (más de 10M descargas/mes)
  - Soporte completo de SMTP con TLS/SSL
  - Permite correos HTML con imágenes y adjuntos
  - Manejo de errores robusto con excepciones
  - Alternativa a la función `mail()` nativa que no funciona en entornos Docker
