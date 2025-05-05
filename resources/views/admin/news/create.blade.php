@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <h1>Thêm bài viết mới</h1>
  <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label>Tiêu đề</label>
      <input type="text" name="title"
             class="form-control @error('title') is-invalid @enderror"
             value="{{ old('title') }}">
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label>Ảnh (thumbnail)</label>
      <input type="file" name="thumbnail"
             class="form-control @error('thumbnail') is-invalid @enderror">
      @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label>Nội dung</label>
      <textarea id="content" name="content"
                class="form-control @error('content') is-invalid @enderror"
                rows="10">{{ old('content') }}</textarea>
      @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button class="btn btn-success">Lưu</button>
  </form>
</div>
@endsection  {{-- ← Đóng section chính ở đây --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (!window.tinymce) return;

  tinymce.init({
    selector: '#content',
    height: 400,
    menubar: false,

    plugins: [
      'advlist autolink lists link image charmap preview anchor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table paste help wordcount'
    ].join(' '),

    toolbar: [
      'undo redo | fontfamily fontselect | fontsize fontsizeselect | blocks |',
      'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify |',
      'bullist numlist outdent indent | link image media | removeformat | code'
    ].join(' '),

    font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
    fontsize_formats:   '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',

    font_family_formats: [
      'Baloo 2="Baloo 2",cursive',
      'Arial=Arial,Helvetica,sans-serif',
      'Helvetica=Helvetica,Arial,sans-serif',
      'Verdana=Verdana,Geneva,sans-serif',
      'Tahoma=Tahoma,Arial,sans-serif',
      'Times New Roman=Times New Roman,serif',
      'Georgia=Georgia,serif',
      'Courier New=Courier New,courier'
    ].join(';'),

    images_upload_url: '{{ route("admin.news.uploadImage") }}?_token={{ csrf_token() }}',
    automatic_uploads: true,
    images_upload_credentials: true,

    content_style: `
      @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap');
      body { font-family: "Baloo 2", cursive; font-size: 14px; }
      img  { max-width: 100%; height: auto; }
    `
  });
});
</script>
@endpush
