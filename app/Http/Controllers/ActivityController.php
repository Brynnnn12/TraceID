<?php

namespace App\Http\Controllers;

use App\Enums\VerificationType;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function index(Request $request)
    {
        return view('activities.index', [
            'activities' => $this->activityService->paginate($request->only(['search', 'type', 'from', 'to'])),
            'filters' => $request->only(['search', 'type', 'from', 'to']),
            'types' => VerificationType::cases(),
        ]);
    }
}
