<div class="d-flex align-items-center mb-3">
    <a href="dia_no_laborable.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo día no laborable</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">
        <form method="POST" action="dia_no_laborable.php?accion=crear" novalidate id="frm">
            <?php $vals = $_POST; require __DIR__ . '/_form_fields.php'; ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
