<div class="d-flex align-items-center mb-3">
    <a href="rol_permiso.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Asignar permiso a rol</h2>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width:580px;">
    <div class="card-body">
        <form method="POST" action="rol_permiso.php?accion=crear" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                <select name="id_rol" class="form-select" required id="selRol">
                    <option value="">— Selecciona un rol —</option>
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id_rol'] ?>"
                        <?= (int)($_POST['id_rol'] ?? 0) === (int)$r['id_rol'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre_rol']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un rol.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Permiso <span class="text-danger">*</span></label>
                <select name="id_permiso" class="form-select" required id="selPermiso">
                    <option value="">— Selecciona un permiso —</option>
                    <?php
                    $moduloActual = null;
                    foreach ($permisos as $p):
                        if ($p['modulo'] !== $moduloActual):
                            if ($moduloActual !== null) echo '</optgroup>';
                            $moduloActual = $p['modulo'];
                            echo '<optgroup label="' . htmlspecialchars($p['modulo'] ?? 'Sin módulo') . '">';
                        endif;
                    ?>
                    <option value="<?= $p['id_permiso'] ?>"
                        <?= (int)($_POST['id_permiso'] ?? 0) === (int)$p['id_permiso'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_permiso']) ?>
                    </option>
                    <?php endforeach; ?>
                    <?php if ($moduloActual !== null) echo '</optgroup>'; ?>
                </select>
                <div class="form-text">Los permisos están agrupados por módulo.</div>
                <div class="invalid-feedback">Selecciona un permiso.</div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar asignación</button>
        </form>
    </div>
</div>

<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    ['selRol','selPermiso'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el.value) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (!ok) e.preventDefault();
});
</script>
