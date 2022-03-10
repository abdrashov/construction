<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AuditLogsController extends Controller
{
    public function index()
    {
        return Inertia::render('Logs/Index', [
            'filters' => Request::all('search', 'trashed'),
            'logs' => Activity::orderByDesc('id')
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($log) => [
                    "log_name" => $log->log_name,
                    "description" => $log->description,
                    "subject_type" => $log->subject_type,
                    "event" => $log->event,
                    "subject_id" => $log->subject_id,
                    "user" => $log->subject->user?->last_name .  '' . $log->subject->user?->first_name,
                    "role" => $log->subject->user?->role,
                    "batch_uuid" => $log->batch_uuid,
                    "created_at" => $log->created_at->format('H:i d/m/Y'),
                ]),
        ]);
    }
}
