<div class="d-flex align-items-center mb-3">
    <a href="pago.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar pago</h2>
</div>
<?php if (!$pago): ?>
<div class="alert alert-warning">Pago no encontrado.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:700px;">
    <div class="card-body">
        <form method="POST" action="pago.php?accion=actualizar&id=<?= $pago['id_pago'] ?>" novalidate id="frm">
            <?php $vals = $pago; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar pago</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
