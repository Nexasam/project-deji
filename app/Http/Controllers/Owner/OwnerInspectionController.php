<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Operations\BookingOperationsService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerInspectionController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, BookingOperationsService $operations, string $task): RedirectResponse
    {
        $data = $request->validate([
            'result' => ['required', 'in:passed,failed'],
            'findings' => ['nullable', 'required_if:result,failed', 'string', 'max:3000'],
            'recommendations' => ['nullable', 'string', 'max:3000'],
            'corrective_action' => ['nullable', 'required_if:result,failed', 'in:cleaning,maintenance'],
            'evidence' => ['nullable', 'array', 'max:8'],
            'evidence.*' => ['image', 'max:8192'],
        ]);
        $data['evidence'] = $request->file('evidence', []);
        $record = $context->business->operationalTasks()->findOrFail($task);
        $operations->completeInspection($record, $request->user(), $data);

        return back()->with('status', $data['result'] === 'passed' ? 'Inspection passed. The property is ready.' : 'Inspection failed. Corrective work was created.');
    }
}
