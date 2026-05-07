<div class="d-flex align-items-center mb-3">
    <a href="especialidad.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar especialidad</h2>
</div>
<?php if (!$especialidad): ?>
<div class="alert alert-warning">Especialidad no encontrada.</div>
<?php else: ?>
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="especialidad.php?accion=actualizar&id=<?= $especialidad['id_especialidad'] ?>" novalidate id="frm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre_especialidad" class="form-control" maxlength="100" required
                       value="<?= htmlspecialchars($especialidad['nombre_especialidad']) ?>">
                <div class="invalid-feedback">El nombre es obligatorio (máx. 100 caracteres).</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" maxlength="1000"><?= htmlspecialchars($especialidad['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" <?= $especialidad['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Activa</label>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const n = this.querySelector('[name=nombre_especialidad]');
    if (!n.value.trim()) { n.classList.add('is-invalid'); e.preventDefault(); }
    else n.classList.remove('is-invalid');
});
</script>
<?php endif; ?>
