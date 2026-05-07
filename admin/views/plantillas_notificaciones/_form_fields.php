<?php $canalesValidos = ['Email','SMS','Sistema']; ?>
<div class="row g-3">

    <div class="col-md-5">
        <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
        <input type="text" name="codigo_plantilla" class="form-control text-uppercase" maxlength="50" required
               placeholder="CONFIRMACION_CITA"
               value="<?= htmlspecialchars($vals['codigo_plantilla'] ?? '') ?>"
               oninput="this.value=this.value.toUpperCase()">
        <div class="form-text">Solo mayúsculas, números y guiones bajos. Ej: <code>RECORDATORIO_CITA</code></div>
        <div class="invalid-feedback" id="err-codigo">El código es obligatorio y solo puede tener mayúsculas, números y _.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre_plantilla" class="form-control" maxlength="100" required
               value="<?= htmlspecialchars($vals['nombre_plantilla'] ?? '') ?>">
        <div class="invalid-feedback">El nombre es obligatorio.</div>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Canal <span class="text-danger">*</span></label>
        <select name="tipo_canal" class="form-select" required id="selCanal">
            <option value="">— Selecciona —</option>
            <?php foreach ($canalesValidos as $c): ?>
            <option value="<?= $c ?>" <?= ($vals['tipo_canal'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un canal.</div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
        <input type="text" name="asunto" class="form-control" maxlength="200" required
               placeholder="Ej: Confirmación de tu cita — {{fecha}}"
               value="<?= htmlspecialchars($vals['asunto'] ?? '') ?>">
        <div class="invalid-feedback">El asunto es obligatorio.</div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Cuerpo del mensaje <span class="text-danger">*</span></label>
        <textarea name="cuerpo_mensaje" class="form-control" rows="8" required
                  placeholder="Hola {{nombre_paciente}}, tu cita con el Dr. {{nombre_medico}} está confirmada para el {{fecha}} a las {{hora}}."><?= htmlspecialchars($vals['cuerpo_mensaje'] ?? '') ?></textarea>
        <div class="form-text">Puedes usar variables como <code>{{nombre_paciente}}</code>, <code>{{nombre_medico}}</code>, <code>{{fecha}}</code>, <code>{{hora}}</code>, <code>{{consultorio}}</code>.</div>
        <div class="invalid-feedback">El cuerpo del mensaje es obligatorio.</div>
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="activa" id="activa" class="form-check-input" value="1"
                   <?= ($vals['activa'] ?? true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="activa">Plantilla activa</label>
        </div>
    </div>

</div>
<script>
(function() {
    const frm = document.getElementById('frm');
    if (!frm) return;
    frm.addEventListener('submit', function(e) {
        let ok = true;
        const campos = [
            { el: frm.querySelector('[name=codigo_plantilla]'), test: v => /^[A-Z0-9_]+$/.test(v.trim()) },
            { el: frm.querySelector('[name=nombre_plantilla]'), test: v => v.trim() !== '' },
            { el: document.getElementById('selCanal'),          test: v => v !== '' },
            { el: frm.querySelector('[name=asunto]'),           test: v => v.trim() !== '' },
            { el: frm.querySelector('[name=cuerpo_mensaje]'),   test: v => v.trim() !== '' },
        ];
        campos.forEach(({ el, test }) => {
            if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
            else el.classList.remove('is-invalid');
        });
        if (!ok) e.preventDefault();
    });
})();
</script>
