@extends('backend.layouts.app')

@section('title', 'Tinh thanh')
@section('page_title', 'Tinh thanh')
@section('breadcrumb', 'Tinh thanh')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Danh sach tinh thanh</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma</th>
                            <th>Loai</th>
                            <th>Ten</th>
                            <th>So phuong xa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($provinces as $province)
                            <tr>
                                <td>{{ $province->code }}</td>
                                <td>{{ $province->type ?: '-' }}</td>
                                <td>{{ $province->name }}</td>
                                <td>{{ $province->wards_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chua co du lieu tinh thanh.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $provinces->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
