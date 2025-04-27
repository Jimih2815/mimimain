document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sb-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.target;
        const ul = document.getElementById(id);
        ul.classList.toggle('sb-show');
      });
    });
  });
  