<div class="d-flex align-items-center mb-3">
    <a href="horario_medico.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar horario</h2>
</div>
<?php if (!$horario): ?>
<div class="alert alert-warning">Horario no encontrado.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:700px;">
    <div class="card-body">
        <form method="POST" action="horario_medico.php?accion=actualizar&id=<?= $horario['id_horario'] ?>" novalidate id="frm">
            <?php $vals = $horario; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar horario</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
