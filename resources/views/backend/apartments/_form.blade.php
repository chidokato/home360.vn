@csrf

@php
    $existingGalleryImages = $galleryImages ?? collect();
    $storedPrice = old('price', null);
    $storedPriceUnit = old('price_unit', null);

    if ($storedPrice === null && isset($apartment) && $apartment->price !== null) {
        if ((float) $apartment->price >= 1000000000) {
            $storedPrice = rtrim(rtrim(number_format((float) $apartment->price / 1000000000, 2, '.', ''), '0'), '.');
            $storedPriceUnit = 'ty';
        } else {
            $storedPrice = rtrim(rtrim(number_format((float) $apartment->price / 1000000, 2, '.', ''), '0'), '.');
            $storedPriceUnit = 'trieu';
        }
    }

    if ($storedPriceUnit === null) {
        $storedPriceUnit = 'ty';
    }
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="card border">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Du an</label>
                            <select id="project_id" name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">Chon du an</option>
                                @foreach ($projects as $id => $name)
                                    <option value="{{ $id }}" {{ (string) old('project_id', $selectedProjectId ?? ($apartment->project_id ?? '')) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="name" class="form-label">Ten can ho</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $apartment->name ?? '') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Gia ban</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ $storedPrice }}" placeholder="Vi du: 2.5">
                                <select name="price_unit" class="form-select" style="max-width: 110px;">
                                    <option value="ty" {{ $storedPriceUnit === 'ty' ? 'selected' : '' }}>Ty</option>
                                    <option value="trieu" {{ $storedPriceUnit === 'trieu' ? 'selected' : '' }}>Trieu</option>
                                </select>
                            </div>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="area" class="form-label">Dien tich (m2)</label>
                            <input type="number" step="0.01" min="0" id="area" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $apartment->area ?? '') }}">
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bedroom_count" class="form-label">Phong ngu</label>
                            <input type="number" min="0" id="bedroom_count" name="bedroom_count" class="form-control @error('bedroom_count') is-invalid @enderror" value="{{ old('bedroom_count', $apartment->bedroom_count ?? '') }}">
                            @error('bedroom_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bathroom_count" class="form-label">WC</label>
                            <input type="number" min="0" id="bathroom_count" name="bathroom_count" class="form-control @error('bathroom_count') is-invalid @enderror" value="{{ old('bathroom_count', $apartment->bathroom_count ?? '') }}">
                            @error('bathroom_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-0">
                            <label for="content" class="form-label">Noi dung</label>
                            <textarea id="content" name="content" rows="8" class="form-control editor @error('content') is-invalid @enderror">{{ old('content', $apartment->content ?? '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Hinh anh can ho</h5>
                <button type="button" class="btn btn-primary btn-sm gallery-picker-trigger">Them anh</button>
            </div>
            <div class="card-body">
                <input type="file" id="gallery_files" name="gallery_files[]" class="d-none" accept="image/*" multiple>
                <div id="gallery-preview-grid" class="d-flex flex-wrap gap-2">
                    @foreach ($existingGalleryImages as $image)
                        <div class="position-relative border rounded overflow-hidden bg-light gallery-item" data-gallery-item style="width: 88px; height: 88px;">
                            <img src="{{ asset($image->image) }}" alt="Apartment image" class="w-100 h-100 object-fit-cover">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center remove-gallery-item" style="width: 20px; height: 20px; line-height: 1;" data-existing-id="{{ $image->id }}">x</button>
                        </div>
                    @endforeach
                </div>
                <div class="form-text mt-3">Ban co the chon nhieu anh cho mot can ho.</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card border">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $apartment->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Hien thi can ho</label>
                </div>

                @if (isset($apartment))
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="save_stay" name="save_stay" value="1" {{ old('save_stay') ? 'checked' : '' }}>
                        <label class="form-check-label" for="save_stay">Luu va o lai trang sua</label>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var galleryInput = document.getElementById('gallery_files');
        var galleryGrid = document.getElementById('gallery-preview-grid');
        var galleryButton = document.querySelector('.gallery-picker-trigger');
        var galleryTransfer = galleryInput ? new DataTransfer() : null;

        function loadImage(file) {
            return new Promise(function (resolve, reject) {
                var image = new Image();
                var objectUrl = URL.createObjectURL(file);

                image.onload = function () {
                    URL.revokeObjectURL(objectUrl);
                    resolve(image);
                };

                image.onerror = function () {
                    URL.revokeObjectURL(objectUrl);
                    reject(new Error('Khong doc duoc anh.'));
                };

                image.src = objectUrl;
            });
        }

        function canvasToBlob(canvas, type, quality) {
            return new Promise(function (resolve, reject) {
                canvas.toBlob(function (blob) {
                    if (!blob) {
                        reject(new Error('Khong the nen anh.'));
                        return;
                    }

                    resolve(blob);
                }, type, quality);
            });
        }

        async function compressImage(file) {
            if (!file || !file.type || file.type.indexOf('image/') !== 0 || file.type === 'image/gif') {
                return file;
            }

            var image = await loadImage(file);
            var ratio = Math.min(1800 / image.width, 1800 / image.height, 1);
            var targetWidth = Math.max(1, Math.round(image.width * ratio));
            var targetHeight = Math.max(1, Math.round(image.height * ratio));

            if (ratio === 1 && file.size <= 1024 * 1024) {
                return file;
            }

            var canvas = document.createElement('canvas');
            canvas.width = targetWidth;
            canvas.height = targetHeight;

            var context = canvas.getContext('2d', { alpha: true });
            context.drawImage(image, 0, 0, targetWidth, targetHeight);

            var blob = await canvasToBlob(canvas, 'image/webp', 0.82);
            var fileName = (file.name || 'image').replace(/\.[^.]+$/, '') + '.webp';

            if (blob.size >= file.size && ratio === 1) {
                return file;
            }

            return new File([blob], fileName, {
                type: blob.type,
                lastModified: Date.now()
            });
        }

        function bindDropZone(element, onFiles) {
            if (!element) {
                return;
            }

            ['dragenter', 'dragover'].forEach(function (eventName) {
                element.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    element.classList.add('border-primary');
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
                element.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    element.classList.remove('border-primary');
                });
            });

            element.addEventListener('drop', function (event) {
                var files = Array.from((event.dataTransfer && event.dataTransfer.files) || []).filter(function (file) {
                    return file.type && file.type.indexOf('image/') === 0;
                });

                if (files.length) {
                    onFiles(files);
                }
            });
        }

        function appendGalleryItem(src, existingId, fileToken) {
            if (!galleryGrid) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'position-relative border rounded overflow-hidden bg-light gallery-item';
            wrapper.setAttribute('data-gallery-item', '');
            wrapper.style.width = '88px';
            wrapper.style.height = '88px';

            var image = document.createElement('img');
            image.src = src;
            image.className = 'w-100 h-100 object-fit-cover';
            image.alt = 'Apartment image';

            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center remove-gallery-item';
            button.style.width = '20px';
            button.style.height = '20px';
            button.style.lineHeight = '1';
            button.textContent = 'x';

            if (existingId) {
                button.dataset.existingId = existingId;
            }

            if (fileToken) {
                button.dataset.fileToken = fileToken;
            }

            wrapper.appendChild(image);
            wrapper.appendChild(button);
            galleryGrid.appendChild(wrapper);
        }

        async function addGalleryFiles(files) {
            if (!galleryInput || !galleryTransfer) {
                return;
            }

            for (let index = 0; index < files.length; index++) {
                let compressedFile = await compressImage(files[index]);
                let fileToken = [compressedFile.name, compressedFile.size, compressedFile.lastModified].join('__');
                galleryTransfer.items.add(compressedFile);

                var reader = new FileReader();
                reader.onload = function (event) {
                    appendGalleryItem(event.target.result, null, fileToken);
                };
                reader.readAsDataURL(compressedFile);
            }

            galleryInput.files = galleryTransfer.files;
        }

        if (galleryButton && galleryInput) {
            galleryButton.addEventListener('click', function () {
                galleryInput.click();
            });

            galleryInput.addEventListener('change', function (event) {
                addGalleryFiles(Array.from(event.target.files || []));
            });
        }

        bindDropZone(galleryGrid, addGalleryFiles);

        document.addEventListener('click', function (event) {
            var removeButton = event.target.closest('.remove-gallery-item');

            if (!removeButton || !galleryInput || !galleryGrid) {
                return;
            }

            var existingId = removeButton.getAttribute('data-existing-id');
            var fileToken = removeButton.getAttribute('data-file-token');

            if (existingId) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_gallery_images[]';
                input.value = existingId;
                galleryGrid.appendChild(input);
            }

            if (fileToken && galleryTransfer) {
                var nextFiles = new DataTransfer();

                Array.from(galleryInput.files).forEach(function (file) {
                    var currentToken = [file.name, file.size, file.lastModified].join('__');

                    if (currentToken !== fileToken) {
                        nextFiles.items.add(file);
                    }
                });

                galleryTransfer = nextFiles;
                galleryInput.files = galleryTransfer.files;
            }

            var item = removeButton.closest('[data-gallery-item]');
            if (item) {
                item.remove();
            }
        });
    });
</script>
