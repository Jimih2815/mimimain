<div id="backToTopFull" class="back-to-top-full">
    
        <i class="fa-solid fa-angles-up"></i>
    
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const btn  = document.getElementById('backToTopFull');
  if (!btn) return;

  const icon = btn.querySelector('i.fa-angles-up');

  // 1) Ẩn/hiện container khi scroll
  window.addEventListener('scroll', () => {
    if (window.scrollY > 200) btn.classList.add('show');
    else btn.classList.remove('show');
  });

  // 2) Khi hover icon → bật active để reveal background
  icon.addEventListener('mouseenter', () => {
    btn.classList.add('active');
  });
  icon.addEventListener('mouseleave', () => {
    btn.classList.remove('active');
  });

  // 3) Chỉ click icon mới scroll lên top
  icon.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});
</script>
@endpush
