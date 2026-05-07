<div class="d-flex align-items-center mb-3">
    <a href="plantilla_notificacion.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar plantilla</h2>
</div>
<?php if (!$plantilla): ?>
<div class="alert alert-warning">Plantilla no encontrada.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:780px;">
    <div class="card-body">
        <form method="POST" action="plantilla_notificacion.php?accion=actualizar&id=<?= $plantilla['id_plantilla'] ?>" novalidate id="frm">
            <?php $vals = $plantilla; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar plantilla</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
