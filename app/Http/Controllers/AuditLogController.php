<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('auditable_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        
        // Get unique users and model types for filters
        $users = \App\Models\User::all();
        $modelTypes = AuditLog::select('auditable_type')->distinct()->pluck('auditable_type');

        return view('audit-logs.index', compact('logs', 'users', 'modelTypes'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        $differences = $log->getDifferences();

        return view('audit-logs.show', compact('log', 'differences'));
    }
}
