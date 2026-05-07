<div class="d-flex align-items-center mb-3">
    <a href="comentario_interno.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo comentario interno</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="comentario_interno.php?accion=crear" novalidate id="frm">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Autor <span class="text-danger">*</span></label>
                    <select name="id_usuario_autor" class="form-select" required id="selAutor">
                        <option value="">— Selecciona usuario —</option>
                        <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"
                            <?= (int)($_POST['id_usuario_autor'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nombre_completo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona un autor.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Paciente relacionado</label>
                    <select name="id_paciente" class="form-select">
                        <option value="">— Sin paciente específico —</option>
                        <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id_paciente'] ?>"
                            <?= (int)($_POST['id_paciente'] ?? 0) === (int)$p['id_paciente'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre_completo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° de cita relacionada</label>
                    <input type="number" name="id_cita" class="form-control" min="1"
                           placeholder="Opcional"
                           value="<?= htmlspecialchars($_POST['id_cita'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Comentario <span class="text-danger">*</span></label>
                    <textarea name="comentario" class="form-control" rows="4" required id="txtComentario"
                              placeholder="Escribe aquí la nota o comentario interno…"><?= htmlspecialchars($_POST['comentario'] ?? '') ?></textarea>
                    <div class="invalid-feedback">El comentario no puede estar vacío.</div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="es_importante" id="es_importante"
                               class="form-check-input" value="1"
                               <?= isset($_POST['es_importante']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-danger fw-semibold" for="es_importante">
                            ⚑ Marcar como importante
                        </label>
                    </div>
                </div>

            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar comentario</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const autor = document.getElementById('selAutor');
    const texto = document.getElementById('txtComentario');
    if (!autor.value) { autor.classList.add('is-invalid'); ok = false; }
    else autor.classList.remove('is-invalid');
    if (!texto.value.trim()) { texto.classList.add('is-invalid'); ok = false; }
    else texto.classList.remove('is-invalid');
    if (!ok) e.preventDefault();
});
</script>
