<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LogActivity::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->ajax()) {
            $perPage = $request->per_page ?? 25;
            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage(),
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
            ]);
        }

        $logs = $query->paginate(25);
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.activity-log.index', compact('logs', 'users'));
    }
}
