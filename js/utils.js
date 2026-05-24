// =====================================================================
// MediCitas — Utilidades compartidas
// =====================================================================

window.MC = window.MC || {};

// ---------------------------------------------------------------------
// Carga de navegación (reemplaza fetch('nav-user.html') etc del repo)
// ---------------------------------------------------------------------
MC.loadNav = async function (file) {
  const container = document.getElementById('nav-container');
  if (!container) return;
  try {
    const res = await fetch(file);
    container.innerHTML = await res.text();
    document.dispatchEvent(new CustomEvent('mc:nav-loaded'));
    // Marcar el item activo basado en el archivo actual
    const here = location.pathname.split('/').pop() || 'index.html';
    container.querySelectorAll('a[href]').forEach(a => {
      const href = a.getAttribute('href');
      if (href === here || (here === 'index.html' && href === '/')) {
        a.setAttribute('data-active', 'true');
      }
    });
  } catch (e) {
    console.warn('No se pudo cargar', file, e);
  }
};

// ---------------------------------------------------------------------
// Helpers de fecha/hora (reutilizando lógica del repo original)
// ---------------------------------------------------------------------
MC.drLabel = (g) => g === 'M' ? 'Dr.' : g === 'F' ? 'Dra.' : 'Dr(a).';

MC.horaLabel = (h) => {
  if (!h) return '';
  const [hh, mm] = h.split(':');
  const n = parseInt(hh);
  return `${n > 12 ? n - 12 : n || 12}:${mm} ${n >= 12 ? 'PM' : 'AM'}`;
};

MC.fechaLarga = (isoFecha) => {
  if (!isoFecha) return '';
  const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
  const dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
  const d = new Date(isoFecha + 'T00:00:00');
  return `${dias[d.getDay()]} ${d.getDate()} de ${meses[d.getMonth()]}, ${d.getFullYear()}`;
};

MC.fechaCorta = (isoFecha) => {
  if (!isoFecha) return '';
  const meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
  const d = new Date(isoFecha + 'T00:00:00');
  return { day: d.getDate(), month: meses[d.getMonth()], year: d.getFullYear() };
};

MC.diasParaCita = (fechaStr) => {
  const hoy = new Date(); hoy.setHours(0,0,0,0);
  const fecha = new Date(fechaStr + 'T00:00:00');
  const diff = Math.round((fecha - hoy) / 86400000);
  if (diff === 0) return 'Tu cita es hoy';
  if (diff === 1) return 'Tu próxima cita es mañana';
  if (diff < 0)   return `Hace ${-diff} días`;
  return `En ${diff} días`;
};

// ---------------------------------------------------------------------
// Verificación de sesión (reemplaza el fetch repetido del repo original)
// ---------------------------------------------------------------------
MC.requireAuth = async function (rol) {
  try {
    const d = await fetch('/api/auth-status.php').then(r => r.json());
    if (!d.loggedIn) { window.location.replace('/index.html'); return null; }
    if (rol && d.rol !== rol) { window.location.replace('/index.html'); return null; }
    return d;
  } catch (e) {
    console.warn('No se pudo verificar sesión', e);
    return null;
  }
};

// ---------------------------------------------------------------------
// Iniciales para avatares
// ---------------------------------------------------------------------
MC.iniciales = (nombre) => {
  if (!nombre) return '?';
  return nombre.split(' ').filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase();
};

// ---------------------------------------------------------------------
// Toast / mensaje rápido (no bloqueante)
// ---------------------------------------------------------------------
MC.toast = function (mensaje, tipo = 'success') {
  const existing = document.querySelector('.mc-toast');
  if (existing) existing.remove();
  const el = document.createElement('div');
  el.className = `mc-toast mc-toast-${tipo}`;
  el.textContent = mensaje;
  document.body.appendChild(el);
  setTimeout(() => el.classList.add('show'), 10);
  setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }, 3200);
};
