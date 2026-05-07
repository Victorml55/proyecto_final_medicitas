<div class="d-flex align-items-center mb-3">
    <a href="dia_no_laborable.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar día no laborable</h2>
</div>
<?php if (!$dia): ?>
<div class="alert alert-warning">Registro no encontrado.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">
        <form method="POST" action="dia_no_laborable.php?accion=actualizar&id=<?= $dia['id_dia_no_laborable'] ?>" novalidate id="frm">
            <?php $vals = $dia; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
