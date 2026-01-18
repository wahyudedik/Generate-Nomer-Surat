<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class LetterActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $openDrafts = Letter::where('type', 'out')
            ->where('status', Letter::STATUS_DRAFT)
            ->with(['creator', 'activityLogs' => function ($query) {
                $query->latest();
            }])
            ->latest()
            ->get();

        $users = User::orderBy('name')->get();
        $actions = LetterActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $types = ['out' => 'Surat Keluar', 'in' => 'Surat Masuk'];
        $statuses = [
            Letter::STATUS_DRAFT => 'Draft',
            Letter::STATUS_COMPLETE => 'Selesai',
        ];

        $logsQuery = LetterActivityLog::with(['letter', 'user'])->latest();

        if ($request->filled('user_id')) {
            $logsQuery->where('user_id', $request->string('user_id')->toString());
        }

        if ($request->filled('action')) {
            $logsQuery->where('action', $request->string('action')->toString());
        }

        if ($request->filled('letter_type')) {
            $type = $request->string('letter_type')->toString();
            $logsQuery->whereHas('letter', function ($query) use ($type) {
                $query->where('type', $type);
            });
        }

        if ($request->filled('letter_status')) {
            $status = $request->string('letter_status')->toString();
            $logsQuery->whereHas('letter', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        if ($request->filled('start_date')) {
            $logsQuery->whereDate('created_at', '>=', $request->string('start_date')->toString());
        }

        if ($request->filled('end_date')) {
            $logsQuery->whereDate('created_at', '<=', $request->string('end_date')->toString());
        }

        if ($request->string('export')->toString() === 'csv') {
            $filename = 'letter-activity-logs-' . now()->format('Ymd-His') . '.csv';

            $callback = function () use ($logsQuery) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Waktu', 'User', 'Jenis', 'Status', 'Nomor', 'Aksi', 'Catatan']);

                $logsQuery->orderBy('created_at', 'desc')
                    ->chunk(200, function ($logs) use ($handle) {
                        foreach ($logs as $log) {
                            fputcsv($handle, [
                                $log->created_at->format('Y-m-d H:i'),
                                $log->user?->name ?? '-',
                                $log->letter?->type ?? '-',
                                $log->letter?->status ?? '-',
                                $log->letter?->number ?? '-',
                                $log->action,
                                $log->note ?? '-',
                            ]);
                        }
                    });

                fclose($handle);
            };

            return Response::streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $recentLogs = $logsQuery->paginate(25)->withQueryString();

        return view('letters.logs.index', compact('openDrafts', 'recentLogs', 'users', 'actions', 'types', 'statuses'));
    }
}
