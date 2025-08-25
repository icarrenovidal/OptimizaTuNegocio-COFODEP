document.querySelectorAll('.logout-link').forEach(link => {
  link.addEventListener('click', function(e) {
    if (!confirm('¿Seguro que quieres cerrar sesión?')) {
      e.preventDefault();
    }
  });
});
