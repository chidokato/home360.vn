@extends('backend.layouts.app')

@section('title', $typeLabel)
@section('page_title', $typeLabel)
@section('breadcrumb', $typeLabel)

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Quan ly {{ strtolower($typeLabel) }}</h4>
            <a href="{{ route($type === 'product' ? 'backend.products.create' : 'backend.news.create') }}" class="btn btn-primary">
                Them {{ strtolower($typeLabel) }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tieu de</th>
                            <th>Category</th>
                            @if ($type === 'product')
                                <th>Gia</th>
                                <th>Du an noi bat</th>
                            @endif
                            <th>Trang thai</th>
                            <th>Ngay dang</th>
                            <th class="text-end">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $post->title }}</div>
                                    <div class="text-muted small">{{ $post->slug }}</div>
                                </td>
                                <td>{{ optional($post->category)->name ?: '-' }}</td>
                                @if ($type === 'product')
                                    <td>{{ $post->price !== null ? number_format((float) $post->price, 0, ',', '.') . ' d' : '-' }}</td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button
                                                type="button"
                                                class="status-toggle {{ $post->is_featured ? 'is-active' : 'is-inactive' }}"
                                                data-toggle-status
                                                data-url="{{ route('backend.products.toggle-featured', $post) }}"
                                                aria-pressed="{{ $post->is_featured ? 'true' : 'false' }}"
                                            ></button>
                                            <span class="status-toggle-label {{ $post->is_featured ? 'text-success' : 'text-danger' }}" data-status-label>
                                                {{ $post->is_featured ? 'Bat' : 'Tat' }}
                                            </span>
                                        </div>
                                    </td>
                                @endif
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <button
                                            type="button"
                                            class="status-toggle {{ $post->is_active ? 'is-active' : 'is-inactive' }}"
                                            data-toggle-status
                                            data-url="{{ route($type === 'product' ? 'backend.products.toggle-status' : 'backend.news.toggle-status', $post) }}"
                                            aria-pressed="{{ $post->is_active ? 'true' : 'false' }}"
                                        ></button>
                                        <span class="status-toggle-label {{ $post->is_active ? 'text-success' : 'text-danger' }}" data-status-label>
                                            {{ $post->is_active ? 'Hien thi' : 'An' }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-end">
                                    @if ($type === 'product')
                                        <a href="{{ route('backend.apartments.index', ['project_id' => $post->id]) }}" class="btn btn-sm btn-soft-info">Can ho</a>
                                    @endif
                                    <a href="{{ route($type === 'product' ? 'backend.products.edit' : 'backend.news.edit', $post) }}" class="btn btn-sm btn-soft-warning">Sua</a>
                                    <form action="{{ route($type === 'product' ? 'backend.products.destroy' : 'backend.news.destroy', $post) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-message="Ban co chac muon xoa {{ strtolower($typeLabel) }} nay?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Xoa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $type === 'product' ? 7 : 5 }}" class="text-center text-muted py-4">
                                    Chua co {{ strtolower($typeLabel) }} nao.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $posts->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
