{{-- Partial reutilizable: tabla de medicamentos dinámica --}}
<div class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-semibold mb-0">Medicamentos <span class="text-danger">*</span></label>
        <button type="button" class="btn btn-sm btn-outline-success" id="btnAgregar">+ Agregar medicamento</button>
    </div>
    <div id="contenedor-meds">
        <?php foreach ($filasMeds as $i => $med): ?>
        <div class="card mb-2 fila-med">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label form-label-sm fw-semibold">Medicamento <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_medicamento[]" class="form-control form-control-sm" maxlength="200" required
                               value="<?= htmlspecialchars($med['nombre_medicamento'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Presentación</label>
                        <input type="text" name="presentacion[]" class="form-control form-control-sm" maxlength="100"
                               value="<?= htmlspecialchars($med['presentacion'] ?? '') ?>" placeholder="cápsulas, jarabe…">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm fw-semibold">Dosis <span class="text-danger">*</span></label>
                        <input type="text" name="dosis[]" class="form-control form-control-sm" maxlength="100" required
                               value="<?= htmlspecialchars($med['dosis'] ?? '') ?>" placeholder="500 mg">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm fw-semibold">Frecuencia <span class="text-danger">*</span></label>
                        <input type="text" name="frecuencia[]" class="form-control form-control-sm" maxlength="100" required
                               value="<?= htmlspecialchars($med['frecuencia'] ?? '') ?>" placeholder="cada 8 hrs">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-quitar" title="Quitar">✕</button>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-sm">Duración</label>
                        <input type="text" name="duracion[]" class="form-control form-control-sm" maxlength="50"
                               value="<?= htmlspecialchars($med['duracion'] ?? '') ?>" placeholder="5 días">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label form-label-sm">Indicaciones especiales</label>
                        <input type="text" name="indicaciones_med[]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($med['indicaciones'] ?? '') ?>" placeholder="Tomar con alimentos…">
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div id="sin-meds" class="text-danger small mt-1" style="display:none">Agrega al menos un medicamento con nombre, dosis y frecuencia.</div>
</div>

<script>
const plantilla = `<div class="card mb-2 fila-med">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label form-label-sm fw-semibold">Medicamento <span class="text-danger">*</span></label>
                <input type="text" name="nombre_medicamento[]" class="form-control form-control-sm" maxlength="200"></div>
            <div class="col-md-3"><label class="form-label form-label-sm">Presentación</label>
                <input type="text" name="presentacion[]" class="form-control form-control-sm" maxlength="100" placeholder="cápsulas, jarabe…"></div>
            <div class="col-md-2"><label class="form-label form-label-sm fw-semibold">Dosis <span class="text-danger">*</span></label>
                <input type="text" name="dosis[]" class="form-control form-control-sm" maxlength="100" placeholder="500 mg"></div>
            <div class="col-md-2"><label class="form-label form-label-sm fw-semibold">Frecuencia <span class="text-danger">*</span></label>
                <input type="text" name="frecuencia[]" class="form-control form-control-sm" maxlength="100" placeholder="cada 8 hrs"></div>
            <div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-quitar" title="Quitar">✕</button></div>
            <div class="col-md-4"><label class="form-label form-label-sm">Duración</label>
                <input type="text" name="duracion[]" class="form-control form-control-sm" maxlength="50" placeholder="5 días"></div>
            <div class="col-md-8"><label class="form-label form-label-sm">Indicaciones especiales</label>
                <input type="text" name="indicaciones_med[]" class="form-control form-control-sm" placeholder="Tomar con alimentos…"></div>
        </div>
    </div>
</div>`;

document.getElementById('btnAgregar').addEventListener('click', function() {
    document.getElementById('contenedor-meds').insertAdjacentHTML('beforeend', plantilla);
    bindQuitarBtns();
});

function bindQuitarBtns() {
    document.querySelectorAll('.btn-quitar').forEach(btn => {
        btn.onclick = function() {
            const filas = document.querySelectorAll('.fila-med');
            if (filas.length > 1) this.closest('.fila-med').remove();
        };
    });
}
bindQuitarBtns();

// Validación del formulario padre
const frmReceta = document.getElementById('frm');
if (frmReceta) {
    frmReceta.addEventListener('submit', function(e) {
        let hayMed = false;
        const nombres = this.querySelectorAll('[name="nombre_medicamento[]"]');
        const dosis   = this.querySelectorAll('[name="dosis[]"]');
        const freq    = this.querySelectorAll('[name="frecuencia[]"]');
        nombres.forEach((n, i) => {
            if (n.value.trim() && dosis[i].value.trim() && freq[i].value.trim()) hayMed = true;
        });
        const aviso = document.getElementById('sin-meds');
        if (!hayMed) { aviso.style.display = 'block'; e.preventDefault(); }
        else aviso.style.display = 'none';
    });
}
</script>
