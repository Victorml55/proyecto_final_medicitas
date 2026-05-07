<div class="d-flex align-items-center mb-3">
    <a href="consultorio.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo consultorio</h2>
</div>
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="consultorio.php?accion=crear" novalidate id="frm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Número de consultorio <span class="text-danger">*</span></label>
                <input type="text" name="numero_consultorio" class="form-control" maxlength="10" required
                       value="<?= htmlspecialchars($_POST['numero_consultorio'] ?? '') ?>">
                <div class="form-text">Máx. 10 caracteres. Ej: 101, A-2. Debe ser único.</div>
                <div class="invalid-feedback">El número de consultorio es obligatorio.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Piso</label>
                <input type="number" name="piso" class="form-control" min="0" max="99"
                       value="<?= htmlspecialchars($_POST['piso'] ?? '') ?>">
                <div class="invalid-feedback">El piso debe ser un número entre 0 y 99.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" checked>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const num = this.querySelector('[name=numero_consultorio]');
    const piso = this.querySelector('[name=piso]');
    if (!num.value.trim()) { num.classList.add('is-invalid'); ok = false; } else num.classList.remove('is-invalid');
    if (piso.value !== '' && (isNaN(piso.value) || piso.value < 0 || piso.value > 99)) { piso.classList.add('is-invalid'); ok = false; } else piso.classList.remove('is-invalid');
    if (!ok) e.preventDefault();
});
</script>
