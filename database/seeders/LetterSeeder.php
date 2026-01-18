<?php

namespace Database\Seeders;

use App\Models\Letter;
use App\Models\LetterActivityLog;
use App\Models\LetterFormat;
use App\Models\LetterFormatSegment;
use App\Models\User;
use App\Services\LetterNumberGenerator;
use Illuminate\Database\Seeder;

class LetterSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $index => $seedUser) {
            if (! $seedUser->unit_code) {
                $seedUser->update(['unit_code' => 'UNIT' . ($index + 1)]);
            }
        }

        $format = $this->seedFormat(
            'Surat Keluar Umum',
            'Format umum surat keluar per unit.',
            'out',
            'month',
            'unit',
            [
                ['kind' => 'sequence', 'value' => null, 'padding' => 3],
                ['kind' => 'text', 'value' => '/SK/', 'padding' => 0],
                ['kind' => 'unit_code', 'value' => null, 'padding' => 0],
                ['kind' => 'text', 'value' => '/', 'padding' => 0],
                ['kind' => 'month_roman', 'value' => null, 'padding' => 0],
                ['kind' => 'text', 'value' => '/', 'padding' => 0],
                ['kind' => 'year', 'value' => null, 'padding' => 0],
            ]
        );

        $formatUndangan = $this->seedFormat(
            'Surat Undangan',
            'Format surat undangan (global tahunan).',
            'out',
            'year',
            'global',
            [
                ['kind' => 'sequence', 'value' => null, 'padding' => 2],
                ['kind' => 'text', 'value' => '/UND/', 'padding' => 0],
                ['kind' => 'month_number', 'value' => null, 'padding' => 0],
                ['kind' => 'text', 'value' => '/', 'padding' => 0],
                ['kind' => 'year', 'value' => null, 'padding' => 0],
            ]
        );

        $formatKeputusan = $this->seedFormat(
            'Surat Keputusan',
            'Format SK (global tanpa reset).',
            'out',
            'all',
            'global',
            [
                ['kind' => 'sequence', 'value' => null, 'padding' => 4],
                ['kind' => 'text', 'value' => '/SKP/', 'padding' => 0],
                ['kind' => 'year_roman', 'value' => null, 'padding' => 0],
            ]
        );

        $generator = app(LetterNumberGenerator::class);
        $formats = [$format->fresh('segments'), $formatUndangan, $formatKeputusan];

        foreach ($users as $userIndex => $seedUser) {
            foreach ($formats as $formatItem) {
                for ($i = 1; $i <= 3; $i++) {
                    $letter = $generator->generate($formatItem, $seedUser);
                    $letter->update([
                        'title' => "Surat Keluar {$formatItem->name} #{$i}",
                        'description' => 'Contoh surat keluar hasil generate.',
                        'issued_at' => now()->subDays(($userIndex * 3) + $i),
                        'scan_path' => 'letters/sample-out.pdf',
                        'status' => Letter::STATUS_COMPLETE,
                    ]);

                    $this->log($letter->id, $seedUser->id, 'draft_created', 'Generate nomor surat keluar.');
                    $this->log($letter->id, $seedUser->id, 'completed', 'Upload scan surat keluar.');
                }
            }
        }

        $draftUser = $users->first();
        $draftLetter = $generator->generate($format->fresh('segments'), $draftUser);
        $this->log($draftLetter->id, $draftUser->id, 'draft_created', 'Generate nomor surat keluar.');

        for ($i = 1; $i <= 10; $i++) {
            $letterIn = Letter::create([
                'type' => 'in',
                'title' => "Surat Masuk Contoh {$i}",
                'description' => 'Contoh surat masuk dengan arsip scan.',
                'issued_at' => now()->subDays(10 + $i),
                'scan_path' => 'letters/sample-in.pdf',
                'status' => Letter::STATUS_COMPLETE,
                'created_by' => $users->random()->id,
            ]);

            $this->log($letterIn->id, $letterIn->created_by, 'completed', 'Upload scan surat masuk.');
        }
    }

    private function seedFormat(
        string $name,
        string $description,
        string $type,
        string $periodMode,
        string $counterScope,
        array $segments
    ): LetterFormat {
        $format = LetterFormat::updateOrCreate(
            ['name' => $name],
            [
                'type' => $type,
                'period_mode' => $periodMode,
                'counter_scope' => $counterScope,
                'description' => $description,
            ]
        );

        $format->segments()->delete();

        foreach ($segments as $index => $segment) {
            LetterFormatSegment::create([
                'format_id' => $format->id,
                'order' => $index + 1,
                'kind' => $segment['kind'],
                'value' => $segment['value'],
                'padding' => $segment['padding'],
            ]);
        }

        return $format->fresh('segments');
    }

    private function log(int $letterId, int $userId, string $action, string $note): void
    {
        LetterActivityLog::create([
            'letter_id' => $letterId,
            'user_id' => $userId,
            'action' => $action,
            'note' => $note,
        ]);
    }
}
