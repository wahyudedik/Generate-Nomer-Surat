<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterActivityLog;
use App\Models\LetterFormat;
use App\Services\LetterNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class LetterOutController extends Controller
{
    public function index(): View
    {
        $letters = Letter::where('type', 'out')->latest()->paginate(10);
        $formats = LetterFormat::where('type', 'out')->with('segments')->orderBy('name')->get();
        $draftLetter = Letter::where('type', 'out')
            ->where('status', Letter::STATUS_DRAFT)
            ->with('creator')
            ->latest()
            ->first();
        $hasDraft = $draftLetter !== null;
        $draftLog = $draftLetter?->activityLogs()->latest()->first();

        return view('letters.out.index', compact('letters', 'formats', 'hasDraft', 'draftLetter', 'draftLog'));
    }

    public function generate(Request $request, LetterNumberGenerator $generator): RedirectResponse
    {
        $request->validate([
            'format_id' => ['required', 'exists:letter_formats,id'],
        ]);

        $format = LetterFormat::where('type', 'out')->findOrFail($request->input('format_id'));

        try {
            $letter = $generator->generate($format, $request->user());
        } catch (RuntimeException $exception) {
            return back()->withErrors(['letter' => $exception->getMessage()]);
        }

        LetterActivityLog::create([
            'letter_id' => $letter->id,
            'user_id' => $request->user()->id,
            'action' => 'draft_created',
            'note' => 'Generate nomor surat keluar.',
        ]);

        return redirect()->route('letters.out.edit', $letter);
    }

    public function edit(Letter $letter): View
    {
        abort_if($letter->type !== 'out', 404);

        return view('letters.out.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter): RedirectResponse
    {
        abort_if($letter->type !== 'out', 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'issued_at' => ['required', 'date'],
            'scan' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        if ($request->hasFile('scan')) {
            if ($letter->scan_path) {
                Storage::disk('public')->delete($letter->scan_path);
            }

            $letter->scan_path = $request->file('scan')->store('letters', 'public');
        }

        $letter->fill([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'issued_at' => $data['issued_at'],
            'status' => Letter::STATUS_COMPLETE,
        ])->save();

        LetterActivityLog::create([
            'letter_id' => $letter->id,
            'user_id' => $request->user()->id,
            'action' => 'completed',
            'note' => 'Upload scan surat keluar.',
        ]);

        return redirect()->route('letters.out.index')->with('status', 'Surat keluar tersimpan.');
    }
}
