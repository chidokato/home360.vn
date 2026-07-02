@extends('backend.layouts.app')

@section('title', 'Pages')
@section('page_title', 'Pages')
@section('breadcrumb', 'Pages')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Quan ly pages</h4>
            <a href="{{ route('backend.pages.create') }}" class="btn btn-primary">Them page</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tieu de</th>
                            <th>URL</th>
                            <th>Trang thai</th>
                            <th>Ngay dang</th>
                            <th class="text-end">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pages as $page)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $page->title }}</div>
                                    <div class="text-muted small">{{ $page->slug }}</div>
                                </td>
                                <td>
                                    <a href="{{ $page->frontend_url }}" target="_blank" rel="noopener">{{ $page->frontend_url }}</a>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <button
                                            type="button"
                                            class="status-toggle {{ $page->is_active ? 'is-active' : 'is-inactive' }}"
                                            data-toggle-status
                                            data-url="{{ route('backend.pages.toggle-status', $page) }}"
                                            aria-pressed="{{ $page->is_active ? 'true' : 'false' }}"
                                        ></button>
                                        <span class="status-toggle-label {{ $page->is_active ? 'text-success' : 'text-danger' }}" data-status-label>
                                            {{ $page->is_active ? 'Hien thi' : 'An' }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $page->published_at ? $page->published_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('backend.pages.edit', $page) }}" class="btn btn-sm btn-soft-warning">Sua</a>
                                    <form action="{{ route('backend.pages.destroy', $page) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-message="Ban co chac muon xoa page nay?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Xoa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chua co page nao.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pages->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
