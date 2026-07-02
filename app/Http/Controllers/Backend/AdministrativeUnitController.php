<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class AdministrativeUnitController extends Controller
{
    public function provinces()
    {
        $provinces = Province::query()
            ->withCount('wards')
            ->orderBy('code')
            ->paginate(20);

        return view('backend.administrative-units.provinces', [
            'provinces' => $provinces,
        ]);
    }

    public function wards(Request $request)
    {
        $provinceId = $request->filled('province_id') ? (int) $request->input('province_id') : null;
        $keyword = trim((string) $request->input('keyword', ''));

        $wards = Ward::query()
            ->with('province')
            ->when($provinceId, fn ($query) => $query->where('province_id', $provinceId))
            ->when($keyword !== '', fn ($query) => $query->where('name', 'like', '%' . $keyword . '%'))
            ->orderBy('province_id')
            ->orderBy('code')
            ->paginate(30)
            ->withQueryString();

        return view('backend.administrative-units.wards', [
            'wards' => $wards,
            'provinceId' => $provinceId,
            'keyword' => $keyword,
            'provinces' => Province::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }
}
