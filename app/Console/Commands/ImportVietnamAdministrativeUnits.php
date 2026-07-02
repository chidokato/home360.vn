<?php

namespace App\Console\Commands;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class ImportVietnamAdministrativeUnits extends Command
{
    protected $signature = 'admin-units:import {file=storage/app/admin-units-2025.xlsx}';

    protected $description = 'Import Vietnam administrative units from the official 2025 XLSX appendix.';

    public function handle(): int
    {
        $file = base_path($this->argument('file'));

        if (! file_exists($file)) {
            $this->error('Khong tim thay file: ' . $file);

            return self::FAILURE;
        }

        $zip = new ZipArchive();

        if ($zip->open($file) !== true) {
            $this->error('Khong mo duoc file XLSX.');

            return self::FAILURE;
        }

        $sharedStrings = $this->loadSharedStrings($zip);
        $provinceRows = $this->parseSheetRows($zip, 'xl/worksheets/sheet1.xml', $sharedStrings);
        $wardRows = $this->parseSheetRows($zip, 'xl/worksheets/sheet2.xml', $sharedStrings);
        $zip->close();

        $provinces = [];
        $provinceMap = [];

        foreach ($provinceRows as $row) {
            $code = trim((string) ($row['B'] ?? ''));
            $name = trim((string) ($row['C'] ?? ''));

            if ($code === '' || $name === '' || ! preg_match('/^\d+$/', $code)) {
                continue;
            }

            $type = $this->extractType($name);

            $provinces[] = [
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'source_file' => basename($file),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $provinceMap[$name] = $code;
            $provinceMap[$this->normalizeProvinceName($name)] = $code;
        }

        $wards = [];

        foreach ($wardRows as $row) {
            $code = trim((string) ($row['B'] ?? ''));
            $name = trim((string) ($row['C'] ?? ''));
            $provinceName = trim((string) ($row['D'] ?? ''));

            if ($code === '' || $name === '' || $provinceName === '' || ! preg_match('/^\d+$/', $code)) {
                continue;
            }

            $provinceCode = $provinceMap[$provinceName] ?? $provinceMap[$this->normalizeProvinceName($provinceName)] ?? null;

            if (! $provinceCode) {
                $this->warn('Bo qua xa/phuong khong tim thay tinh: ' . $name . ' / ' . $provinceName);
                continue;
            }

            $wards[] = [
                'province_code' => $provinceCode,
                'code' => $code,
                'name' => $name,
                'type' => $this->extractType($name),
            ];
        }

        DB::transaction(function () use ($provinces, $wards, $file) {
            Province::query()->delete();

            foreach (array_chunk($provinces, 500) as $chunk) {
                Province::query()->insert($chunk);
            }

            $provinceIdsByCode = Province::query()->pluck('id', 'code');
            $now = now();
            $wardPayload = [];

            foreach ($wards as $ward) {
                $provinceId = $provinceIdsByCode[$ward['province_code']] ?? null;

                if (! $provinceId) {
                    continue;
                }

                $wardPayload[] = [
                    'province_id' => $provinceId,
                    'code' => $ward['code'],
                    'name' => $ward['name'],
                    'type' => $ward['type'],
                    'source_file' => basename($file),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            Ward::query()->delete();

            foreach (array_chunk($wardPayload, 500) as $chunk) {
                Ward::query()->insert($chunk);
            }
        });

        $this->info('Da import ' . Province::query()->count() . ' tinh/thanh va ' . Ward::query()->count() . ' phuong/xa.');

        return self::SUCCESS;
    }

    protected function loadSharedStrings(ZipArchive $zip): array
    {
        $xml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
        $strings = [];

        foreach ($xml->si as $si) {
            if (isset($si->t)) {
                $strings[] = trim((string) $si->t);
                continue;
            }

            $text = '';

            foreach ($si->r as $run) {
                $text .= (string) $run->t;
            }

            $strings[] = trim($text);
        }

        return $strings;
    }

    protected function parseSheetRows(ZipArchive $zip, string $sheetPath, array $sharedStrings): array
    {
        $sheet = simplexml_load_string($zip->getFromName($sheetPath));
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $item = [];

            foreach ($row->c as $cell) {
                preg_match('/^[A-Z]+/', (string) $cell['r'], $matches);
                $column = $matches[0] ?? null;

                if (! $column) {
                    continue;
                }

                $value = isset($cell->v) ? trim((string) $cell->v) : '';

                if ((string) $cell['t'] === 's') {
                    $value = trim($sharedStrings[(int) $value] ?? '');
                }

                $item[$column] = $value;
            }

            $rows[] = $item;
        }

        return $rows;
    }

    protected function extractType(string $name): ?string
    {
        foreach (['Thành phố', 'Tỉnh', 'Phường', 'Xã', 'Đặc khu'] as $type) {
            if (str_starts_with($name, $type . ' ')) {
                return $type;
            }
        }

        return null;
    }

    protected function normalizeProvinceName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', trim($name));
        $prefixes = ['Tỉnh ', 'Thành phố ', 'TP ', 'Tp '];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return trim(substr($name, strlen($prefix)));
            }
        }

        return $name;
    }
}
