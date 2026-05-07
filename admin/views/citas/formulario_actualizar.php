<div class="d-flex align-items-center mb-3">
    <a href="cita.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar cita #<?= $cita['id_cita'] ?? '' ?></h2>
</div>
<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (!$cita): ?>
<div class="alert alert-warning">Cita no encontrada.</div>
<?php else: ?>
<div class="card shadow-sm" style="max-width:800px;">
    <div class="card-body">
        <form method="POST" action="cita.php?accion=actualizar&id=<?= $cita['id_cita'] ?>" novalidate id="frmCita">
            <?php $vals = !empty($error) ? $_POST : $cita; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar cita</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
