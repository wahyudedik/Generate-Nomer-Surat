<?php

namespace App\Services;

use App\Models\Letter;
use App\Models\LetterCounter;
use App\Models\LetterFormat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LetterNumberGenerator
{
    public function generate(LetterFormat $format, User $user): Letter
    {
        $format->loadMissing('segments');

        if (Letter::where('type', $format->type)
            ->where('status', Letter::STATUS_DRAFT)
            ->exists()) {
            throw new RuntimeException('Surat sebelumnya belum selesai.');
        }

        return DB::transaction(function () use ($format, $user) {
            $period = $this->resolvePeriod($format);
            $unitCode = $this->resolveUnitCode($format, $user);

            $counter = LetterCounter::where('format_id', $format->id)
                ->where('period', $period)
                ->where('unit_code', $unitCode)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = LetterCounter::create([
                    'format_id' => $format->id,
                    'period' => $period,
                    'unit_code' => $unitCode,
                    'current_number' => 0,
                ]);
            }

            $counter->current_number++;
            $counter->save();

            $sequence = $counter->current_number;
            $number = $this->composeNumber($format, $sequence, $user);

            return Letter::create([
                'type' => $format->type,
                'format_id' => $format->id,
                'number' => $number,
                'sequence' => $sequence,
                'status' => Letter::STATUS_DRAFT,
                'created_by' => $user->id,
            ]);
        });
    }

    private function resolvePeriod(LetterFormat $format): string
    {
        return match ($format->period_mode) {
            'month' => now()->format('Y-m'),
            'year' => now()->format('Y'),
            default => 'all',
        };
    }

    private function resolveUnitCode(LetterFormat $format, User $user): string
    {
        if ($format->counter_scope !== 'unit' && ! $format->segments->contains('kind', 'unit_code')) {
            return '';
        }

        $unitCode = trim((string) $user->unit_code);

        if ($unitCode === '') {
            throw new RuntimeException('Kode unit belum diisi.');
        }

        return $unitCode;
    }

    private function composeNumber(LetterFormat $format, int $sequence, User $user): string
    {
        $parts = [];

        foreach ($format->segments as $segment) {
            $parts[] = match ($segment->kind) {
                'sequence' => str_pad((string) $sequence, $segment->padding, '0', STR_PAD_LEFT),
                'text' => $segment->value ?? '',
                'unit_code' => $user->unit_code ?? '',
                'day' => now()->format('d'),
                'month_roman' => $this->monthToRoman((int) now()->format('m')),
                'month_number' => now()->format('m'),
                'year' => now()->format('Y'),
                'year_roman' => $this->intToRoman((int) now()->format('Y')),
                default => $segment->value ?? '',
            };
        }

        return implode('', $parts);
    }

    private function monthToRoman(int $month): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $map[$month] ?? '';
    }

    private function intToRoman(int $number): string
    {
        if ($number <= 0) {
            return '';
        }

        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];

        $result = '';

        foreach ($map as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }
}
