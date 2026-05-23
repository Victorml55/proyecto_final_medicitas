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
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                    <select name="id_rol" class="form-select" required id="id_rol">
                        <option value="">— Selecciona un rol —</option>
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_rol'] ?>"
                                data-nombre="<?= htmlspecialchars(strtolower($r['nombre_rol'])) ?>"
                                <?= (($_POST['id_rol'] ?? '') == $r['id_rol']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nombre_rol']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Debes seleccionar un rol.</div>
                </div>
            </div>

            <!-- Sección médico: se muestra solo cuando el rol es Médico -->
            <div id="seccion-medico" style="display:none;">
                <hr class="my-4">
                <h5 class="fw-semibold mb-3">Datos del perfil médico</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Especialidad <span class="text-danger">*</span></label>
                        <select name="id_especialidad" class="form-select" id="id_especialidad">
                            <option value="">— Selecciona —</option>
                            <?php foreach ($especialidades as $e): ?>
                            <option value="<?= $e['id_especialidad'] ?>"
                                    <?= (($_POST['id_especialidad'] ?? '') == $e['id_especialidad']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre_especialidad']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">La especialidad es obligatoria.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cédula profesional <span class="text-danger">*</span></label>
                        <input type="text" name="cedula_profesional" class="form-control" maxlength="20"
                               value="<?= htmlspecialchars($_POST['cedula_profesional'] ?? '') ?>">
                        <div class="invalid-feedback">La cédula es obligatoria.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Costo consulta ($) <span class="text-danger">*</span></label>
                        <input type="number" name="costo_consulta" class="form-control" min="0" step="0.01"
                               value="<?= htmlspecialchars($_POST['costo_consulta'] ?? '') ?>">
                        <div class="invalid-feedback">El costo es obligatorio.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Duración consulta (min)</label>
                        <input type="number" name="duracion_consulta" class="form-control" min="5" max="180" value="<?= htmlspecialchars($_POST['duracion_consulta'] ?? '30') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Años de experiencia</label>
                        <input type="number" name="años_experiencia" class="form-control" min="0" max="60"
                               value="<?= htmlspecialchars($_POST['años_experiencia'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Universidad</label>
                        <input type="text" name="universidad" class="form-control" maxlength="150"
                               value="<?= htmlspecialchars($_POST['universidad'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Biografía</label>
                        <textarea name="biografia" class="form-control" rows="2"><?= htmlspecialchars($_POST['biografia'] ?? '') ?></textarea>
                    </div>

                    <!-- Horarios -->
                    <div class="col-12 mt-2">
                        <label class="form-label fw-semibold">Horarios de atención</label>
                        <p class="text-muted small mb-2">Activa los días que el médico atiende y define su horario.</p>
                        <div class="border rounded" style="overflow:hidden;">
                            <?php
                            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                            $defaultsActivo = ['Lunes'=>true,'Martes'=>true,'Miércoles'=>true,'Jueves'=>true,'Viernes'=>true,'Sábado'=>false,'Domingo'=>false];
                            foreach ($dias as $i => $dia):
                                $checked   = !empty($_POST['horario'][$dia]['hora_inicio']);
                                $hInicio   = htmlspecialchars($_POST['horario'][$dia]['hora_inicio'] ?? '08:00');
                                $hFin      = htmlspecialchars($_POST['horario'][$dia]['hora_fin']    ?? '17:00');
                                $border    = $i > 0 ? 'border-top' : '';
                            ?>
                            <div class="d-flex align-items-center gap-3 px-3 py-2 <?= $border ?>" style="background:#fff;">
                                <div style="min-width:110px;">
                                    <div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input dia-toggle"
                                               id="dia-<?= $dia ?>"
                                               name="horario[<?= $dia ?>][activo]"
                                               value="1"
                                               <?= $checked ? 'checked' : '' ?>
                                               onchange="toggleHorario(this)">
                                        <label class="form-check-label fw-semibold" for="dia-<?= $dia ?>"><?= $dia ?></label>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 horario-inputs" style="<?= $checked ? '' : 'opacity:.4; pointer-events:none;' ?>">
                                    <label class="mb-0 small text-muted">Entrada</label>
                                    <input type="time" name="horario[<?= $dia ?>][hora_inicio]"
                                           class="form-control form-control-sm" style="width:120px;"
                                           value="<?= $hInicio ?>">
                                    <label class="mb-0 small text-muted">Salida</label>
                                    <input type="time" name="horario[<?= $dia ?>][hora_fin]"
                                           class="form-control form-control-sm" style="width:120px;"
                                           value="<?= $hFin ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
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
function toggleHorario(checkbox) {
    const inputs = checkbox.closest('.d-flex').querySelector('.horario-inputs');
    if (checkbox.checked) {
        inputs.style.opacity = '1';
        inputs.style.pointerEvents = 'auto';
    } else {
        inputs.style.opacity = '.4';
        inputs.style.pointerEvents = 'none';
    }
}

const rolSelect     = document.getElementById('id_rol');
const seccionMedico = document.getElementById('seccion-medico');

function esMedico() {
    const opt = rolSelect.options[rolSelect.selectedIndex];
    const nombre = (opt?.dataset?.nombre ?? '').toLowerCase();
    return nombre.includes('médico') || nombre.includes('medico');
}

rolSelect.addEventListener('change', function () {
    seccionMedico.style.display = esMedico() ? 'block' : 'none';
});

// Mostrar sección si se recarga con error y el rol ya estaba seleccionado
if (esMedico()) seccionMedico.style.display = 'block';

document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const campos = [
        { el: this.querySelector('[name=nombre]'),          test: v => v.trim() !== '' },
        { el: this.querySelector('[name=apellido_paterno]'),test: v => v.trim() !== '' },
        { el: this.querySelector('[name=email]'),           test: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
        { el: this.querySelector('[name=password]'),        test: v => v.length >= 8 },
        { el: this.querySelector('[name=telefono]'),        test: v => v === '' || /^[0-9\+\-\s]+$/.test(v) },
        { el: this.querySelector('[name=fecha_nacimiento]'),test: v => v === '' || new Date(v) <= new Date() },
        { el: this.querySelector('[name=id_rol]'),          test: v => v !== '' },
    ];
    if (esMedico()) {
        campos.push(
            { el: this.querySelector('[name=id_especialidad]'),   test: v => v !== '' },
            { el: this.querySelector('[name=cedula_profesional]'),test: v => v.trim() !== '' },
            { el: this.querySelector('[name=costo_consulta]'),    test: v => v !== '' && parseFloat(v) >= 0 },
        );
    }
    campos.forEach(({ el, test }) => {
        if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (!ok) e.preventDefault();
});
</script>
