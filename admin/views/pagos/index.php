<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Pagos</h2>
    <a href="pago.php?accion=crear" class="btn btn-primary">+ Nuevo pago</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cita</th>
                    <th>Paciente</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th>Fecha pago</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagos)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($pagos as $p):
                    $badgeEstado = match($p['estado_pago']) {
                        'Completado'  => 'bg-success',
                        'Pendiente'   => 'bg-warning text-dark',
                        'Cancelado'   => 'bg-danger',
                        'Reembolsado' => 'bg-info text-dark',
                        default       => 'bg-secondary',
                    };
                ?>
                <tr>
                    <td><?= $p['id_pago'] ?></td>
                    <td>
                        <span class="badge bg-secondary">Cita #<?= $p['id_cita'] ?></span>
                        <div class="small text-muted"><?= date('d/m/Y', strtotime($p['fecha_cita'])) ?></div>
                    </td>
                    <td><?= htmlspecialchars($p['nombre_paciente']) ?></td>
                    <td class="fw-semibold">$<?= number_format($p['monto'], 2) ?></td>
                    <td><?= htmlspecialchars($p['metodo_pago']) ?></td>
                    <td><span class="badge <?= $badgeEstado ?>"><?= htmlspecialchars($p['estado_pago']) ?></span></td>
                    <td><?= $p['fecha_pago'] ? date('d/m/Y H:i', strtotime($p['fecha_pago'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="pago.php?accion=actualizar&id=<?= $p['id_pago'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="pago.php?accion=borrar&id=<?= $p['id_pago'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el pago #<?= $p['id_pago'] ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
