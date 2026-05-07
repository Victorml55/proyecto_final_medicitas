<?php
$metodosValidos = ['Efectivo','Tarjeta','Transferencia','Cheque'];
$estadosValidos = ['Pendiente','Completado','Cancelado','Reembolsado'];
?>
<div class="row g-3">

    <div class="col-12">
        <label class="form-label fw-semibold">Cita <span class="text-danger">*</span></label>
        <select name="id_cita" class="form-select" required id="selCita">
            <option value="">— Selecciona una cita —</option>
            <?php foreach ($citas as $c): ?>
            <option value="<?= $c['id_cita'] ?>" <?= (int)($vals['id_cita'] ?? 0) === (int)$c['id_cita'] ? 'selected' : '' ?>>
                Cita #<?= $c['id_cita'] ?> — <?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                — Paciente: <?= htmlspecialchars($c['nombre_paciente']) ?>
                — Dr. <?= htmlspecialchars($c['nombre_medico']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona una cita.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Monto ($) <span class="text-danger">*</span></label>
        <input type="number" name="monto" class="form-control" min="0" step="0.01" required id="inpMonto"
               value="<?= htmlspecialchars($vals['monto'] ?? '') ?>">
        <div class="invalid-feedback">Ingresa un monto válido (≥ 0).</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Método de pago <span class="text-danger">*</span></label>
        <select name="metodo_pago" class="form-select" required id="selMetodo">
            <option value="">— Selecciona —</option>
            <?php foreach ($metodosValidos as $met): ?>
            <option value="<?= $met ?>" <?= ($vals['metodo_pago'] ?? '') === $met ? 'selected' : '' ?>><?= $met ?></option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un método.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Estado del pago</label>
        <select name="estado_pago" class="form-select">
            <?php foreach ($estadosValidos as $est): ?>
            <option value="<?= $est ?>" <?= ($vals['estado_pago'] ?? 'Pendiente') === $est ? 'selected' : '' ?>><?= $est ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Referencia de pago</label>
        <input type="text" name="referencia_pago" class="form-control" maxlength="100"
               placeholder="N° de transacción, folio, etc."
               value="<?= htmlspecialchars($vals['referencia_pago'] ?? '') ?>">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Notas</label>
        <textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($vals['notas'] ?? '') ?></textarea>
    </div>

</div>
<script>
(function() {
    const frm = document.getElementById('frm');
    if (!frm) return;
    frm.addEventListener('submit', function(e) {
        let ok = true;
        const checks = [
            { id: 'selCita',   test: v => v !== '' },
            { id: 'selMetodo', test: v => v !== '' },
        ];
        checks.forEach(({ id, test }) => {
            const el = document.getElementById(id);
            if (!test(el.value)) { el.classList.add('is-invalid'); ok = false; }
            else el.classList.remove('is-invalid');
        });
        const monto = document.getElementById('inpMonto');
        if (monto.value === '' || parseFloat(monto.value) < 0) { monto.classList.add('is-invalid'); ok = false; }
        else monto.classList.remove('is-invalid');
        if (!ok) e.preventDefault();
    });
})();
</script>
