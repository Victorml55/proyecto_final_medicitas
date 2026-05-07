<div class="d-flex align-items-center mb-3">
    <a href="usuario_rol.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar asignación</h2>
</div>

<?php if (!$asignacion): ?>
<div class="alert alert-warning">Asignación no encontrada.</div>
<?php else: ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width:580px;">
    <div class="card-body">
        <form method="POST" action="usuario_rol.php?accion=actualizar&id=<?= $asignacion['id_usuario_rol'] ?>" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                <select name="id_usuario" class="form-select" required id="selUsuario">
                    <option value="">— Selecciona un usuario —</option>
                    <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>"
                        <?= (int)$asignacion['id_usuario'] === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre_completo']) ?>
                        (<?= htmlspecialchars($u['email']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un usuario.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                <select name="id_rol" class="form-select" required id="selRol">
                    <option value="">— Selecciona un rol —</option>
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id_rol'] ?>"
                        <?= (int)$asignacion['id_rol'] === (int)$r['id_rol'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre_rol']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un rol.</div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar asignación</button>
        </form>
    </div>
</div>

<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    ['selUsuario','selRol'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el.value) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (!ok) e.preventDefault();
});
</script>
<?php endif; ?>
