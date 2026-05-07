<div class="d-flex align-items-center mb-3">
    <a href="estado_cita.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo estado de cita</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="estado_cita.php?accion=crear" novalidate id="frm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre_estado" class="form-control" maxlength="50" required
                       value="<?= htmlspecialchars($_POST['nombre_estado'] ?? '') ?>">
                <div class="form-text">Máx. 50 caracteres. Debe ser único. Ej: Confirmada, Cancelada, Pendiente.</div>
                <div class="invalid-feedback">El nombre es obligatorio.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Color (hex)</label>
                <div class="input-group" style="max-width:220px;">
                    <input type="color" name="color_picker" id="color_picker" class="form-control form-control-color" value="#005f99">
                    <input type="text" name="color" id="color_text" class="form-control" maxlength="7" placeholder="#005f99"
                           value="<?= htmlspecialchars($_POST['color'] ?? '') ?>">
                </div>
                <div class="form-text">Formato #RRGGBB. Opcional.</div>
                <div class="invalid-feedback" id="color-error">Debe ser un color hexadecimal válido (#RRGGBB).</div>
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
const picker = document.getElementById('color_picker');
const text   = document.getElementById('color_text');
picker.addEventListener('input', () => text.value = picker.value);
text.addEventListener('input',   () => { if (/^#[0-9A-Fa-f]{6}$/.test(text.value)) picker.value = text.value; });
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const n = this.querySelector('[name=nombre_estado]');
    const c = document.getElementById('color_text');
    if (!n.value.trim()) { n.classList.add('is-invalid'); ok = false; } else n.classList.remove('is-invalid');
    if (c.value.trim() !== '' && !/^#[0-9A-Fa-f]{6}$/.test(c.value.trim())) {
        c.classList.add('is-invalid'); document.getElementById('color-error').style.display='block'; ok = false;
    } else { c.classList.remove('is-invalid'); }
    if (!ok) e.preventDefault();
});
</script>
