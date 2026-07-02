@csrf

<div class="row">
    <div class="col-xl-9">
        <div class="card border">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tieu de</label>
                            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title ?? '') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug ?? '') }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="html_file" class="form-label">Import file HTML tu Ladipage</label>
                            <input type="file" id="html_file" name="html_file" class="form-control @error('html_file') is-invalid @enderror" accept=".html,.htm,text/html">
                            <div class="form-text">Neu chon file, noi dung HTML ben duoi se duoc thay bang noi dung file khi luu.</div>
                            @error('html_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-0">
                            <label for="html_content" class="form-label">Noi dung HTML</label>
                            <textarea id="html_content" name="html_content" rows="18" class="form-control font-monospace @error('html_content') is-invalid @enderror">{{ old('html_content', $page->html_content ?? '') }}</textarea>
                            @error('html_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border">
            <div class="card-header">
                <h5 class="card-title mb-0">Cau hinh SEO</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="seo_title" class="form-label">Title</label>
                            <input type="text" id="seo_title" name="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $page->seo_title ?? '') }}" placeholder="Nhap SEO title">
                            @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="seo_description" class="form-label">Description</label>
                            <textarea id="seo_description" name="seo_description" rows="3" class="form-control @error('seo_description') is-invalid @enderror" placeholder="Nhap SEO description">{{ old('seo_description', $page->seo_description ?? '') }}</textarea>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-2">
                                <label class="form-label mb-0">Hien thi</label>
                            </div>
                            <div class="col-lg-10">
                                <div id="seo-link-preview" class="text-muted">{{ url('/') }}/slug</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card border">
            <div class="card-body">
                <div class="mb-3">
                    <label for="published_at" class="form-label">Ngay dang</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '') }}">
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Hien thi page</label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var slugInput = document.getElementById('slug');
        var seoLinkPreview = document.getElementById('seo-link-preview');

        function updateSeoPreview() {
            if (!slugInput || !seoLinkPreview) {
                return;
            }

            seoLinkPreview.textContent = '{{ url('/') }}/' + (slugInput.value || 'slug');
        }

        if (slugInput) {
            slugInput.addEventListener('input', updateSeoPreview);
        }

        updateSeoPreview();
    });
</script>
