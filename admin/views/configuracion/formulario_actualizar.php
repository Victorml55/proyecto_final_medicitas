<div class="d-flex align-items-center mb-3">
    <a href="configuracion.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar configuración</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if (!$config): ?>
<div class="alert alert-warning">Configuración no encontrada.</div>
<?php else: ?>
<div class="card shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="configuracion.php?accion=actualizar&id=<?= $config['id_config'] ?>" novalidate id="frm">
            <div class="mb-3">
                <label class="form-label fw-semibold">Clave <span class="text-danger">*</span></label>
                <input type="text" name="clave" class="form-control" maxlength="100" required
                       pattern="[a-zA-Z0-9_\-]+"
                       value="<?= htmlspecialchars($config['clave']) ?>">
                <div class="form-text">Solo letras, números, guiones y guiones bajos. Debe ser única.</div>
                <div class="invalid-feedback">La clave es obligatoria y solo puede contener letras, números, _ y -.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipo de dato <span class="text-danger">*</span></label>
                <select name="tipo_dato" class="form-select" id="tipo_dato" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach (['string','int','boolean','json'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($config['tipo_dato'] === $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un tipo de dato.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Valor <span class="text-danger">*</span></label>
                <input type="text" name="valor" id="valor" class="form-control" required
                       value="<?= htmlspecialchars($config['valor']) ?>">
                <div class="form-text" id="valor-hint"></div>
                <div class="invalid-feedback">El valor es obligatorio y debe coincidir con el tipo.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($config['descripcion'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</div>
<script>
const hints = { string:'Cualquier texto.', int:'Solo números enteros.', boolean:'"true" o "false".', json:'JSON válido.' };
const tipoSel = document.getElementById('tipo_dato');
document.getElementById('valor-hint').textContent = hints[tipoSel.value] || '';
tipoSel.addEventListener('change', function() {
    document.getElementById('valor-hint').textContent = hints[this.value] || '';
});
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const clave = this.querySelector('[name=clave]');
    const tipo  = this.querySelector('[name=tipo_dato]');
    const valor = this.querySelector('[name=valor]');
    if (!clave.value.trim() || !/^[a-zA-Z0-9_\-]+$/.test(clave.value.trim())) {
        clave.classList.add('is-invalid'); ok = false;
    } else clave.classList.remove('is-invalid');
    if (!tipo.value) { tipo.classList.add('is-invalid'); ok = false; } else tipo.classList.remove('is-invalid');
    if (!valor.value.trim()) { valor.classList.add('is-invalid'); ok = false; }
    else if (tipo.value === 'int' && !/^\d+$/.test(valor.value.trim())) { valor.classList.add('is-invalid'); ok = false; }
    else if (tipo.value === 'boolean' && !['true','false','1','0'].includes(valor.value.trim())) { valor.classList.add('is-invalid'); ok = false; }
    else if (tipo.value === 'json') { try { JSON.parse(valor.value.trim()); valor.classList.remove('is-invalid'); } catch { valor.classList.add('is-invalid'); ok = false; } }
    else valor.classList.remove('is-invalid');
    if (!ok) e.preventDefault();
});
</script>
<?php endif; ?>
