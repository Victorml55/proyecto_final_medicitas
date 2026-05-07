<div class="d-flex align-items-center mb-3">
    <a href="permiso.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar permiso</h2>
</div>

<?php if (!$permiso): ?>
<div class="alert alert-warning">Permiso no encontrado.</div>
<?php else: ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="permiso.php?accion=actualizar&id=<?= $permiso['id_permiso'] ?>" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre_permiso" class="form-control" maxlength="100" required
                       value="<?= htmlspecialchars($permiso['nombre_permiso']) ?>">
                <div class="form-text">Formato recomendado: <code>modulo.accion</code>. Máx. 100 caracteres.</div>
                <div class="invalid-feedback">El nombre del permiso es obligatorio.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Módulo</label>
                <input type="text" name="modulo" class="form-control" maxlength="50"
                       value="<?= htmlspecialchars($permiso['modulo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($permiso['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                           <?= $permiso['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar permiso</button>
        </form>
    </div>
</div>

<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const n = this.querySelector('[name=nombre_permiso]');
    if (!n.value.trim()) { n.classList.add('is-invalid'); e.preventDefault(); }
    else n.classList.remove('is-invalid');
});
</script>
<?php endif; ?>
