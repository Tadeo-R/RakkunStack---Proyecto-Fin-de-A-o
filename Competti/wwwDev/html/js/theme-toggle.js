const toggleBtn = document.getElementById('theme-toggle');

toggleBtn.addEventListener('click', () => {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  let newTheme = 'dark';

  if (currentTheme === 'dark') {
    newTheme = 'light';
  }

  document.documentElement.setAttribute('data-theme', newTheme);

  actualizarIcono(newTheme);
});

function actualizarIcono(newTheme) {
  const themeIcon = document.getElementById('theme-icon');
  if (newTheme === 'dark') {
    themeIcon.classList.remove('fa-sun');
    themeIcon.classList.add('fa-moon');
  } else {
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
  }
}