<?php
// Variables esperadas: $medicos, $consultorios, $diasValidos
// Valores actuales vienen de $vals (array)
$diasValidos = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
?>
<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">Médico <span class="text-danger">*</span></label>
        <select name="id_medico" class="form-select" required id="selMedico">
            <option value="">— Selecciona médico —</option>
            <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['id_medico'] ?>" <?= (int)($vals['id_medico'] ?? 0) === (int)$m['id_medico'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nombre_medico']) ?> — <?= htmlspecialchars($m['nombre_especialidad']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un médico.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Consultorio</label>
        <select name="id_consultorio" class="form-select">
            <option value="">— Sin asignar —</option>
            <?php foreach ($consultorios as $c): ?>
            <option value="<?= $c['id_consultorio'] ?>" <?= (int)($vals['id_consultorio'] ?? 0) === (int)$c['id_consultorio'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['numero_consultorio']) ?> <?= $c['piso'] ? '(Piso '.$c['piso'].')' : '' ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Día de la semana <span class="text-danger">*</span></label>
        <select name="dia_semana" class="form-select" required id="selDia">
            <option value="">— Selecciona —</option>
            <?php foreach ($diasValidos as $dia): ?>
            <option value="<?= $dia ?>" <?= ($vals['dia_semana'] ?? '') === $dia ? 'selected' : '' ?>><?= $dia ?></option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un día.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
        <input type="time" name="hora_inicio" class="form-control" required id="horaIni"
               value="<?= htmlspecialchars(substr($vals['hora_inicio'] ?? '', 0, 5)) ?>">
        <div class="invalid-feedback">Ingresa la hora de inicio.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Hora fin <span class="text-danger">*</span></label>
        <input type="time" name="hora_fin" class="form-control" required id="horaFin"
               value="<?= htmlspecialchars(substr($vals['hora_fin'] ?? '', 0, 5)) ?>">
        <div class="invalid-feedback">Debe ser mayor que la hora de inicio.</div>
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1"
                   <?= ($vals['activo'] ?? true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="activo">Horario activo</label>
        </div>
    </div>

</div>
<script>
(function() {
    const frm = document.getElementById('frm');
    if (!frm) return;
    frm.addEventListener('submit', function(e) {
        let ok = true;
        ['selMedico','selDia'].forEach(function(id) {
            const el = document.getElementById(id);
            if (!el.value) { el.classList.add('is-invalid'); ok = false; }
            else el.classList.remove('is-invalid');
        });
        const ini = document.getElementById('horaIni');
        const fin = document.getElementById('horaFin');
        if (!ini.value) { ini.classList.add('is-invalid'); ok = false; } else ini.classList.remove('is-invalid');
        if (!fin.value || fin.value <= ini.value) { fin.classList.add('is-invalid'); ok = false; } else fin.classList.remove('is-invalid');
        if (!ok) e.preventDefault();
    });
})();
</script>
