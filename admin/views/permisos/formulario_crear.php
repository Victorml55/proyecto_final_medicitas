<div class="d-flex align-items-center mb-3">
    <a href="permiso.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo permiso</h2>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="permiso.php?accion=crear" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre_permiso" class="form-control" maxlength="100" required
                       placeholder="ej. usuarios.crear"
                       value="<?= htmlspecialchars($_POST['nombre_permiso'] ?? '') ?>">
                <div class="form-text">Usa el formato <code>modulo.accion</code>. Máx. 100 caracteres. Debe ser único.</div>
                <div class="invalid-feedback">El nombre del permiso es obligatorio.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Módulo</label>
                <input type="text" name="modulo" class="form-control" maxlength="50"
                       placeholder="ej. Usuarios, Médicos, Citas…"
                       value="<?= htmlspecialchars($_POST['modulo'] ?? '') ?>">
                <div class="form-text">Agrupa los permisos por área funcional.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"
                          placeholder="Describe qué acción habilita este permiso…"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
                           <?= isset($_POST['activo']) ? 'checked' : 'checked' ?>>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar permiso</button>
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
