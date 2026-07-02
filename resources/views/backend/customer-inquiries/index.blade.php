@extends('backend.layouts.app')

@section('title', 'Khach hang gui form')
@section('page_title', 'Khach hang gui form')
@section('breadcrumb', 'Khach hang gui form')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Danh sach khach hang de lai thong tin</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Khach hang</th>
                            <th>Du an</th>
                            <th>Thong tin lien he</th>
                            <th>Nguon gui</th>
                            <th>Thoi gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inquiries as $inquiry)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $inquiry->name }}</div>
                                    <div class="text-muted small">#{{ $inquiry->id }}</div>
                                </td>
                                <td>
                                    @if ($inquiry->post)
                                        <div class="fw-medium">{{ $inquiry->post->title }}</div>
                                        <div class="text-muted small">{{ optional($inquiry->post->category)->name ?: 'Du an' }}</div>
                                    @else
                                        <div class="fw-medium">{{ $inquiry->project_title ?: '-' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $inquiry->phone }}</div>
                                    <div class="text-muted small">{{ $inquiry->email ?: 'Khong co email' }}</div>
                                </td>
                                <td>
                                    @if ($inquiry->source_url)
                                        <a href="{{ $inquiry->source_url }}" target="_blank" rel="noopener">Trang gui form</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                    @if ($inquiry->download_url)
                                        <div class="small mt-1">
                                            <a href="{{ $inquiry->download_url }}" target="_blank" rel="noopener">File tai xuong</a>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $inquiry->created_at?->format('d/m/Y H:i') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Chua co khach hang nao gui form.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $inquiries->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
