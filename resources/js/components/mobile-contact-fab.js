/**
 * Mobile Contact FAB (Phone/Zalo/Messenger)
 * - Hiện/ẩn theo màn hình bằng CSS
 * - JS chỉ lo toggle + auto-close
 */
export function initMobileContactFab() {
  const fab = document.querySelector('.mcfab');
  if (!fab) return;
  if (fab.dataset.mcfabInited === '1') return;
  fab.dataset.mcfabInited = '1';

  const mainBtn = fab.querySelector('.mcfab__main');
  if (!mainBtn) return;

  const autoCloseMs = Number.parseInt(fab.dataset.autoClose || '8000', 10);
  let timer = null;

  const close = () => {
    fab.classList.remove('is-open');
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
  };

  const scheduleAutoClose = () => {
    if (!Number.isFinite(autoCloseMs) || autoCloseMs <= 0) return;
    if (timer) clearTimeout(timer);
    timer = setTimeout(close, autoCloseMs);
  };

  const open = () => {
    fab.classList.add('is-open');
    scheduleAutoClose();
  };

  const toggle = () => {
    if (fab.classList.contains('is-open')) close();
    else open();
  };

  mainBtn.addEventListener('click', (e) => {
    e.preventDefault();
    toggle();
  });

  // click ra ngoài thì đóng
  document.addEventListener('click', (e) => {
    if (!fab.contains(e.target)) close();
  });

  // Nếu quay về màn hình lớn thì đóng luôn (đỡ bị "kẹt")
  const mql = window.matchMedia('(min-width: 992px)');
  if (mql && typeof mql.addEventListener === 'function') {
    mql.addEventListener('change', (ev) => {
      if (ev.matches) close();
    });
  } else if (mql && typeof mql.addListener === 'function') {
    // Safari cũ
    mql.addListener((ev) => {
      if (ev.matches) close();
    });
  }
}
