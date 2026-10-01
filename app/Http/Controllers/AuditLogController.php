<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latestFirst();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->date('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->date('tanggal_selesai'));
        }

        $logs = $query->paginate(20)->withQueryString();
        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('audit.index', compact('logs', 'actions'));
    }
}
