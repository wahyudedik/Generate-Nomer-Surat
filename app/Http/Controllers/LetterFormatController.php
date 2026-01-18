<?php

namespace App\Http\Controllers;

use App\Models\LetterFormat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LetterFormatController extends Controller
{
    public function index(): View
    {
        $formats = LetterFormat::with('segments')->orderBy('name')->get();

        return view('letters.formats.index', compact('formats'));
    }

    public function create(): View
    {
        return view('letters.formats.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateFormat($request);

        $format = LetterFormat::create([
            'type' => $data['type'],
            'period_mode' => $data['period_mode'],
            'counter_scope' => $data['counter_scope'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $this->syncSegments($format, $data['segments']);

        return redirect()->route('letter-formats.index')->with('status', 'Format berhasil dibuat.');
    }

    public function edit(LetterFormat $letterFormat): View
    {
        $letterFormat->load('segments');

        return view('letters.formats.edit', ['format' => $letterFormat]);
    }

    public function update(Request $request, LetterFormat $letterFormat): RedirectResponse
    {
        $data = $this->validateFormat($request);

        $letterFormat->update([
            'type' => $data['type'],
            'period_mode' => $data['period_mode'],
            'counter_scope' => $data['counter_scope'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $letterFormat->segments()->delete();
        $this->syncSegments($letterFormat, $data['segments']);

        return redirect()->route('letter-formats.index')->with('status', 'Format berhasil diperbarui.');
    }

    public function destroy(LetterFormat $letterFormat): RedirectResponse
    {
        $letterFormat->delete();

        return redirect()->route('letter-formats.index')->with('status', 'Format berhasil dihapus.');
    }

    private function validateFormat(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:in,out'],
            'period_mode' => ['required', 'in:year,month,all'],
            'counter_scope' => ['required', 'in:global,unit'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'segments' => ['required', 'array', 'min:1'],
            'segments.*.kind' => ['nullable', 'string'],
            'segments.*.value' => ['nullable', 'string', 'max:255'],
            'segments.*.padding' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->input('counter_scope') !== 'unit') {
                // continue checking other constraints
            } else {
                $segments = $request->input('segments', []);
                $hasUnit = collect($segments)
                    ->pluck('kind')
                    ->filter()
                    ->contains('unit_code');

                if (! $hasUnit) {
                    $validator->errors()->add('segments', 'Segmen unit_code wajib untuk counter per unit.');
                }
            }

            $segments = $request->input('segments', []);
            $kinds = collect($segments)
                ->pluck('kind')
                ->filter()
                ->values();

            if ($request->input('period_mode') === 'month') {
                $hasMonth = $kinds->contains('month_roman') || $kinds->contains('month_number');

                if (! $hasMonth) {
                    $validator->errors()->add('segments', 'Segmen bulan wajib untuk reset bulanan.');
                }
            }

            if ($request->input('period_mode') === 'year') {
                $hasYear = $kinds->contains('year') || $kinds->contains('year_roman');

                if (! $hasYear) {
                    $validator->errors()->add('segments', 'Segmen tahun wajib untuk reset tahunan.');
                }
            }
        });

        return $validator->validate();
    }

    private function syncSegments(LetterFormat $format, array $segments): void
    {
        $order = 1;

        foreach ($segments as $segment) {
            if (empty($segment['kind'])) {
                continue;
            }

            $format->segments()->create([
                'order' => $order,
                'kind' => $segment['kind'],
                'value' => $segment['value'] ?? null,
                'padding' => (int) ($segment['padding'] ?? 0),
            ]);

            $order++;
        }
    }
}
