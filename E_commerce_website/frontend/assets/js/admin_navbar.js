document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.admin-nav__toggle');
  const links = document.getElementById('adminNavLinks');
  if (!toggle || !links) return;

  const setOpen = (open) => {
    if (open) {
      links.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
    } else {
      links.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  };

  setOpen(false);

  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    setOpen(!links.classList.contains('is-open'));
  });

  links.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (a) setOpen(false);
  });

  document.addEventListener('click', (e) => {
    const nav = e.target.closest('.admin-nav');
    if (!nav) setOpen(false);
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 760) setOpen(false);
  });
});
