<div class="d-flex align-items-center mb-3">
    <a href="usuario.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo usuario</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">
        <form method="POST" action="usuario.php?accion=crear" novalidate id="frm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" maxlength="100" required
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                    <div class="invalid-feedback">El nombre es obligatorio.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control" maxlength="100" required
                           value="<?= htmlspecialchars($_POST['apellido_paterno'] ?? '') ?>">
                    <div class="invalid-feedback">El apellido paterno es obligatorio.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control" maxlength="100"
                           value="<?= htmlspecialchars($_POST['apellido_materno'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" maxlength="150" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div class="invalid-feedback">Ingresa un email válido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                    <div class="form-text">Mínimo 8 caracteres.</div>
                    <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="tel" name="telefono" class="form-control" maxlength="15" pattern="[0-9\+\-\s]+"
                           value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                    <div class="form-text">Solo números, +, - y espacios.</div>
                    <div class="invalid-feedback">Formato de teléfono inválido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           max="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? '') ?>">
                    <div class="invalid-feedback">La fecha no puede ser futura.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Género</label>
                    <select name="genero" class="form-select">
                        <option value="">— Sin especificar —</option>
                        <?php foreach (['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= (($_POST['genero'] ?? '') === $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="activo">Usuario activo</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const campos = [
        { el: this.querySelector('[name=nombre]'),          test: v => v.trim() !== '' },
        { el: this.querySelector('[name=apellido_paterno]'),test: v => v.trim() !== '' },
        { el: this.querySelector('[name=email]'),           test: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
        { el: this.querySelector('[name=password]'),        test: v => v.length >= 8 },
        { el: this.querySelector('[name=telefono]'),        test: v => v === '' || /^[0-9\+\-\s]+$/.test(v) },
        { el: this.querySelector('[name=fecha_nacimiento]'),test: v => v === '' || new Date(v) <= new Date() },
    ];
    campos.forEach(({ el, test }) => {
        if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (!ok) e.preventDefault();
});
</script>
