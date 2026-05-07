<div class="d-flex align-items-center mb-3">
    <a href="rol.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo rol</h2>
</div>
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="rol.php?accion=crear" novalidate id="frm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre_rol" class="form-control" maxlength="50" required
                       value="<?= htmlspecialchars($_POST['nombre_rol'] ?? '') ?>">
                <div class="form-text">Máx. 50 caracteres. Debe ser único.</div>
                <div class="invalid-feedback">El nombre del rol es obligatorio.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const n = this.querySelector('[name=nombre_rol]');
    if (!n.value.trim()) { n.classList.add('is-invalid'); e.preventDefault(); }
    else n.classList.remove('is-invalid');
});
</script>
