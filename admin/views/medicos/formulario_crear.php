<div class="d-flex align-items-center mb-3">
    <a href="medico.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo médico</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="medico.php?accion=crear" enctype="multipart/form-data" novalidate id="frm">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                    <select name="id_usuario" class="form-select" required id="selUsuario">
                        <option value="">— Selecciona usuario —</option>
                        <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= (int)($_POST['id_usuario'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim($u['nombre_completo'])) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona un usuario.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Especialidad <span class="text-danger">*</span></label>
                    <select name="id_especialidad" class="form-select" required id="selEsp">
                        <option value="">— Selecciona especialidad —</option>
                        <?php foreach ($especialidades as $e): ?>
                        <option value="<?= $e['id_especialidad'] ?>" <?= (int)($_POST['id_especialidad'] ?? 0) === (int)$e['id_especialidad'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre_especialidad']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona una especialidad.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cédula profesional <span class="text-danger">*</span></label>
                    <input type="text" name="cedula_profesional" class="form-control" maxlength="20" required
                           value="<?= htmlspecialchars($_POST['cedula_profesional'] ?? '') ?>">
                    <div class="invalid-feedback">La cédula es obligatoria y debe ser única.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Universidad</label>
                    <input type="text" name="universidad" class="form-control" maxlength="150"
                           value="<?= htmlspecialchars($_POST['universidad'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha de inicio de práctica</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                           value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Consultorio asignado</label>
                    <select name="id_consultorio" class="form-select">
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($consultorios as $c): ?>
                        <option value="<?= $c['id_consultorio'] ?>" <?= (int)($_POST['id_consultorio'] ?? 0) === (int)$c['id_consultorio'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['numero_consultorio']) ?><?= $c['piso'] ? ' (Piso '.$c['piso'].')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Se usará por defecto al crear horarios.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Costo consulta ($) <span class="text-danger">*</span></label>
                    <input type="number" name="costo_consulta" class="form-control" min="0" step="0.01" required
                           value="<?= htmlspecialchars($_POST['costo_consulta'] ?? '') ?>">
                    <div class="invalid-feedback">Ingresa un costo válido (≥ 0).</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Duración consulta (min) <span class="text-danger">*</span></label>
                    <input type="number" name="duracion_consulta" class="form-control" min="1" max="240" required
                           value="<?= htmlspecialchars($_POST['duracion_consulta'] ?? '30') ?>">
                    <div class="invalid-feedback">Duración mínima: 1 minuto.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Biografía</label>
                    <textarea name="biografia" class="form-control" rows="3"><?= htmlspecialchars($_POST['biografia'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Foto de perfil</label>
                    <input type="file" name="foto_perfil" class="form-control"
                           accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="form-text">JPG, PNG, GIF o WebP. Máximo 5 MB. Opcional.</div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="activo">Médico activo</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar médico</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const checks = [
        { id: 'selUsuario',  test: v => v !== '' },
        { id: 'selEsp',      test: v => v !== '' },
    ];
    checks.forEach(({ id, test }) => {
        const el = document.getElementById(id);
        if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    const campos = [
        { el: this.querySelector('[name=cedula_profesional]'), test: v => v.trim() !== '' },
        { el: this.querySelector('[name=costo_consulta]'),     test: v => v !== '' && parseFloat(v) >= 0 },
        { el: this.querySelector('[name=duracion_consulta]'),  test: v => parseInt(v) > 0 },
    ];
    campos.forEach(({ el, test }) => {
        if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (!ok) e.preventDefault();
});
</script>
