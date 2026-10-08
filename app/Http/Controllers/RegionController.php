<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Throwable;

class RegionController extends Controller
{
    private const BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/v2';

    public function provinces(): JsonResponse
    {
        try {
            $data = $this->readCache('provinces.json');

            if ($data === null) {
                $data = collect($this->fetch('provinces.json'))
                    ->map(fn ($item) => ['id' => (string) $item['id'], 'name' => (string) $item['name']])
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                $this->writeCache('provinces.json', $data);
            }

            return response()->json($data)->header('Cache-Control', 'public, max-age=86400');
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Data provinsi belum dapat dimuat. Silakan coba kembali.'], 503);
        }
    }

    public function regencies(Request $request): JsonResponse
    {
        $request->validate(['province' => ['required', 'regex:/^\d{2}$/']]);

        try {
            $code = $request->string('province')->toString();
            $file = 'regencies/'.$code.'.json';
            $data = $this->readCache($file);

            if ($data === null) {
                $data = collect($this->fetch('regencies/'.$code.'.json'))
                    ->map(fn ($item) => ['id' => (string) $item['id'], 'name' => (string) $item['name']])
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                $this->writeCache($file, $data);
            }

            return response()->json($data)->header('Cache-Control', 'public, max-age=86400');
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Data kabupaten/kota belum dapat dimuat. Silakan coba kembali.'], 503);
        }
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate(['regency' => ['required', 'regex:/^\d{2}\.\d{2}$/']]);

        try {
            $code = $request->string('regency')->toString();
            $file = 'districts/'.$code.'.json';
            $data = $this->readCache($file);

            if ($data === null) {
                $data = collect($this->fetch('districts/'.$code.'.json'))
                    ->map(fn ($item) => ['id' => (string) $item['id'], 'name' => (string) $item['name']])
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                $this->writeCache($file, $data);
            }

            return response()->json($data)->header('Cache-Control', 'public, max-age=86400');
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Data kecamatan belum dapat dimuat. Silakan coba kembali.'], 503);
        }
    }

    public function villages(Request $request): JsonResponse
    {
        $request->validate(['district' => ['required', 'regex:/^\d{2}\.\d{2}\.\d{2}$/']]);

        try {
            $code = $request->string('district')->toString();
            $file = 'villages/'.$code.'.json';
            $data = $this->readCache($file);

            if ($data === null) {
                $data = collect($this->fetch('villages/'.$code.'.json'))
                    ->map(fn ($item) => ['id' => (string) $item['id'], 'name' => (string) $item['name']])
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                $this->writeCache($file, $data);
            }

            return response()->json($data)->header('Cache-Control', 'public, max-age=86400');
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Data kelurahan/desa belum dapat dimuat. Silakan coba kembali.'], 503);
        }
    }

    private function fetch(string $path): array
    {
        $response = Http::acceptJson()->timeout(20)->retry(2, 250)->get(self::BASE_URL.'/'.$path);

        $response->throw();

        return (array) $response->json('data', []);
    }

    private function cachePath(string $relative): string
    {
        return storage_path('app/regions/'.$relative);
    }

    private function readCache(string $relative): ?array
    {
        $path = $this->cachePath($relative);

        if (!File::exists($path)) return null;

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : null;
    }

    private function writeCache(string $relative, array $data): void
    {
        $path = $this->cachePath($relative);

        File::ensureDirectoryExists(dirname($path));

        File::put($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
