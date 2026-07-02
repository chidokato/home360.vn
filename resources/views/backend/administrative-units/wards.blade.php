@extends('backend.layouts.app')

@section('title', 'Phuong xa')
@section('page_title', 'Phuong xa')
@section('breadcrumb', 'Phuong xa')

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h4 class="card-title mb-0">Danh sach phuong xa</h4>
            <form method="GET" action="{{ route('backend.administrative-units.wards') }}" class="d-flex flex-wrap gap-2">
                <select name="province_id" class="form-select">
                    <option value="">Tat ca tinh thanh</option>
                    @foreach ($provinces as $id => $name)
                        <option value="{{ $id }}" {{ (string) $provinceId === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tim phuong xa">
                <button type="submit" class="btn btn-light">Loc</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma</th>
                            <th>Loai</th>
                            <th>Ten</th>
                            <th>Tinh thanh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($wards as $ward)
                            <tr>
                                <td>{{ $ward->code }}</td>
                                <td>{{ $ward->type ?: '-' }}</td>
                                <td>{{ $ward->name }}</td>
                                <td>{{ optional($ward->province)->name ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chua co du lieu phuong xa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $wards->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
