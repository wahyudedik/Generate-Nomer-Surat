<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterInController extends Controller
{
    public function index(): View
    {
        $letters = Letter::where('type', 'in')->latest()->paginate(10);

        return view('letters.in.index', compact('letters'));
    }

    public function create(): View
    {
        return view('letters.in.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'issued_at' => ['required', 'date'],
            'scan' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $scanPath = $request->file('scan')->store('letters', 'public');

        Letter::create([
            'type' => 'in',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'issued_at' => $data['issued_at'],
            'scan_path' => $scanPath,
            'status' => Letter::STATUS_COMPLETE,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('letters.in.index')->with('status', 'Surat masuk tersimpan.');
    }
}
