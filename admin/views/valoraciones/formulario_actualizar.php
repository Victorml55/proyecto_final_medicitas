<div class="d-flex align-items-center mb-3">
    <a href="valoracion.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar valoración</h2>
</div>
<?php if (!$valoracion): ?>
<div class="alert alert-warning">Valoración no encontrada.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">

        <div class="alert alert-light border mb-4">
            <small class="text-muted">Cita</small>
            <strong class="ms-2">#<?= $valoracion['id_cita'] ?></strong>
            <small class="text-muted ms-3">Solo se pueden editar la calificación, el comentario y la visibilidad.</small>
        </div>

        <form method="POST" action="valoracion.php?accion=actualizar&id=<?= $valoracion['id_valoracion'] ?>" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Calificación <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 align-items-center">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="calificacion"
                               id="cal<?= $i ?>" value="<?= $i ?>"
                               <?= (int)$valoracion['calificacion'] === $i ? 'checked' : '' ?>>
                        <label class="form-check-label fs-5" for="cal<?= $i ?>"><?= $i ?> ★</label>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="invalid-feedback d-block" id="calErr"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Comentario</label>
                <textarea name="comentario" class="form-control" rows="3"><?= htmlspecialchars($valoracion['comentario'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" name="anonimo" id="anonimo" class="form-check-input" value="1"
                           <?= $valoracion['anonimo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="anonimo">Publicar como anónimo</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar valoración</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const cals   = this.querySelectorAll('[name=calificacion]');
    const marcado = [...cals].some(r => r.checked);
    const err    = document.getElementById('calErr');
    if (!marcado) { err.textContent = 'Selecciona una calificación.'; e.preventDefault(); }
    else err.textContent = '';
});
</script>
<?php endif; ?>
