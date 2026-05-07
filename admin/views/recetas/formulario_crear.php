<div class="d-flex align-items-center mb-3">
    <a href="receta.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nueva receta</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card shadow-sm" style="max-width:860px;">
    <div class="card-body">
        <form method="POST" action="receta.php?accion=crear" novalidate id="frm">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Cita <span class="text-danger">*</span></label>
                    <select name="id_cita" class="form-select" required id="selCita">
                        <option value="">— Selecciona la cita —</option>
                        <?php foreach ($citas as $c): ?>
                        <option value="<?= $c['id_cita'] ?>" <?= (int)($_POST['id_cita'] ?? 0) === (int)$c['id_cita'] ? 'selected' : '' ?>>
                            #<?= $c['id_cita'] ?> — <?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                            | Paciente: <?= htmlspecialchars($c['nombre_paciente']) ?>
                            | Dr. <?= htmlspecialchars($c['nombre_medico']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona una cita.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Diagnóstico</label>
                    <textarea name="diagnostico" class="form-control" rows="2"
                              placeholder="Describe el diagnóstico clínico…"><?= htmlspecialchars($_POST['diagnostico'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Indicaciones generales</label>
                    <textarea name="indicaciones_generales" class="form-control" rows="2"
                              placeholder="Reposo, dieta, cuidados generales…"><?= htmlspecialchars($_POST['indicaciones_generales'] ?? '') ?></textarea>
                </div>
            </div>

            <?php
            // Para nueva receta: una fila vacía por defecto
            $filasMeds = [['nombre_medicamento'=>'','presentacion'=>'','dosis'=>'','frecuencia'=>'','duracion'=>'','indicaciones'=>'']];
            require __DIR__ . '/_medicamentos.php';
            ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar receta</button>
            </div>
        </form>
    </div>
</div>

<script>
// Validación cita
document.getElementById('frm').addEventListener('submit', function(e) {
    const sel = document.getElementById('selCita');
    if (!sel.value) { sel.classList.add('is-invalid'); e.preventDefault(); }
    else sel.classList.remove('is-invalid');
}, true); // captura antes del listener de medicamentos
</script>
