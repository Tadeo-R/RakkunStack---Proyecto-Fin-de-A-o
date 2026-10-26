const toggleBtn = document.getElementById('theme-toggle');

// 1. Función unificada para aplicar el tema y actualizar el ícono
function aplicarTema(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme); // Guarda la elección de forma persistente
  actualizarIcono(theme);
}

// 2. Función para actualizar las clases de FontAwesome
function actualizarIcono(theme) {
  const themeIcon = document.getElementById('theme-icon');
  if (!themeIcon) return; // Evita errores si el ícono no ha cargado en el DOM

  if (theme === 'dark') {
    themeIcon.classList.remove('fa-sun');
    themeIcon.classList.add('fa-moon');
  } else {
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
  }
}

// 3. Inicializar el tema al cargar la página (Soluciona el problema de Proxmox)
const temaGuardado = localStorage.getItem('theme');
const prefiereOscuroDelSistema = window.matchMedia('(prefers-color-scheme: dark)').matches;

// Determina qué tema usar: primero el guardado, luego el del sistema operativo, por defecto 'light'
if (temaGuardado === 'dark' || (!temaGuardado && prefiereOscuroDelSistema)) {
  aplicarTema('dark');
} else {
  aplicarTema('light');
}

// 4. Evento Click para alternar entre modos
toggleBtn.addEventListener('click', () => {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  // Si el tema actual es oscuro, el nuevo será claro; de lo contrario, será oscuro
  const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';

  aplicarTema(newTheme);
});
