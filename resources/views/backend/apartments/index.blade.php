@extends('backend.layouts.app')

@section('title', 'Can ho')
@section('page_title', 'Can ho')
@section('breadcrumb', 'Can ho')

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h4 class="card-title mb-0">Quan ly can ho</h4>
            <div class="d-flex flex-wrap gap-2">
                <form method="GET" action="{{ route('backend.apartments.index') }}" class="d-flex gap-2">
                    <select name="project_id" class="form-select">
                        <option value="">Tat ca du an</option>
                        @foreach ($projects as $id => $name)
                            <option value="{{ $id }}" {{ (string) $projectId === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-light">Loc</button>
                </form>
                <a href="{{ route('backend.apartments.create', $projectId ? ['project_id' => $projectId] : []) }}" class="btn btn-primary">
                    Them can ho
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Can ho</th>
                            <th>Du an</th>
                            <th>Gia ban</th>
                            <th>Dien tich</th>
                            <th>Phong ngu</th>
                            <th>WC</th>
                            <th>Trang thai</th>
                            <th class="text-end">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apartments as $apartment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @php
                                            $thumb = optional($apartment->images->first())->image;
                                        @endphp
                                        <div class="flex-shrink-0">
                                            @if ($thumb)
                                                <img src="{{ asset($thumb) }}" alt="{{ $apartment->name }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                                    <i class="ri-image-line"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $apartment->name }}</div>
                                            <div class="text-muted small">{{ $apartment->images->count() }} anh</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($apartment->project)->title ?: '-' }}</td>
                                <td>{{ $apartment->price !== null ? number_format((float) $apartment->price, 0, ',', '.') . ' d' : '-' }}</td>
                                <td>{{ $apartment->area !== null ? rtrim(rtrim(number_format((float) $apartment->area, 2, '.', ''), '0'), '.') . ' m2' : '-' }}</td>
                                <td>{{ $apartment->bedroom_count ?? '-' }}</td>
                                <td>{{ $apartment->bathroom_count ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $apartment->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $apartment->is_active ? 'Hien thi' : 'An' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('backend.apartments.edit', $apartment) }}" class="btn btn-sm btn-soft-warning">Sua</a>
                                    <form action="{{ route('backend.apartments.destroy', $apartment) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-message="Ban co chac muon xoa can ho nay?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Xoa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Chua co can ho nao.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $apartments->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
