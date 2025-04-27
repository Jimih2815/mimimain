document.addEventListener('DOMContentLoaded', function() {
  // 1) Scroll: dính sát top khi header cuộn lên
  const sidebar = document.querySelector('.quan-ly-side-bar');
  const header  = document.querySelector('header');
  const threshold = header ? header.offsetHeight : 0;

  if (sidebar) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > threshold) {
        sidebar.classList.add('scrolled');
      } else {
        sidebar.classList.remove('scrolled');
      }
    });
  }

  // 2) Toggle submenu khi click vào header của mục cha
  document.querySelectorAll('.cha-va-btn').forEach(function(headerBtn) {
    headerBtn.addEventListener('click', function() {
      const btn = headerBtn.querySelector('.sb-toggle');
      if (!btn) return;  // nếu mục cha không có con thì thôi

      const targetId = btn.dataset.target;
      const listEl   = document.getElementById(targetId);

      // Mở/đóng submenu
      listEl.classList.toggle('sb-show');

      // Đổi mũi tên ▾ ↔ ▴ cho vui
      btn.textContent = btn.textContent === '▾' ? '▴' : '▾';
    });
  });
});
