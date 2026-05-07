<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/pago.php');

$app    = new Pago();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

$metodosValidos = ['Efectivo','Tarjeta','Transferencia','Cheque'];
$estadosValidos = ['Pendiente','Completado','Cancelado','Reembolsado'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idCita  = (int)($_POST['id_cita']     ?? 0);
            $monto   = $_POST['monto']             ?? '';
            $metodo  = $_POST['metodo_pago']       ?? '';
            $estado  = $_POST['estado_pago']       ?? 'Pendiente';

            if (!$idCita || $monto === '' || !$metodo) {
                $error = 'Cita, monto y método de pago son obligatorios.'; break;
            }
            if (!is_numeric($monto) || (float)$monto < 0) {
                $error = 'El monto debe ser un número positivo.'; break;
            }
            if (!in_array($metodo, $metodosValidos, true)) { $error = 'Método de pago inválido.'; break; }
            if (!in_array($estado, $estadosValidos, true)) { $error = 'Estado de pago inválido.'; break; }
            $app->crear($_POST);
            header('Location: pago.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idCita  = (int)($_POST['id_cita']     ?? 0);
            $monto   = $_POST['monto']             ?? '';
            $metodo  = $_POST['metodo_pago']       ?? '';
            $estado  = $_POST['estado_pago']       ?? 'Pendiente';

            if (!$idCita || $monto === '' || !$metodo || !$id) {
                $error = 'Cita, monto y método de pago son obligatorios.'; break;
            }
            if (!is_numeric($monto) || (float)$monto < 0) {
                $error = 'El monto debe ser un número positivo.'; break;
            }
            if (!in_array($metodo, $metodosValidos, true)) { $error = 'Método de pago inválido.'; break; }
            if (!in_array($estado, $estadosValidos, true)) { $error = 'Estado de pago inválido.'; break; }
            $data = $_POST;
            $data['id_pago'] = $id;
            $app->actualizar($data);
            header('Location: pago.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) { $app->borrar($id); header('Location: pago.php?accion=leer&ok=borrado'); exit; }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $citas = $app->todasCitas();
        require(__DIR__ . '/views/pagos/formulario_crear.php');
        break;
    case 'actualizar':
        $pago  = $id ? $app->leerUno($id) : null;
        $citas = $app->todasCitas();
        require(__DIR__ . '/views/pagos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $pagos = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Pago registrado correctamente.'],
            'actualizado' => ['success', 'Pago actualizado correctamente.'],
            'borrado'     => ['danger',  'Pago eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/pagos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
