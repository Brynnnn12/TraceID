<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseFile;
use App\Models\VerificationTemplate;
use App\Services\CaseService;

class CaseController extends Controller
{
    public function __construct(private readonly CaseService $caseService) {}

    public function index()
    {
        $cases = CaseFile::query()
            ->with('template')
            ->latest()
            ->paginate(15);

        return view('cases.index', ['cases' => $cases]);
    }

    public function create()
    {
        return view('cases.create', [
            'templates' => $this->activeTemplates(),
        ]);
    }

    public function store(StoreCaseRequest $request)
    {
        $case = $this->caseService->create($request->validated());

        return redirect()
            ->route('cases.show', ['case' => $case->getRouteKey()])
            ->with('status', 'Kasus berhasil dibuat.');
    }

    public function show(CaseFile $case)
    {
        $case->load('template');

        return view('cases.show', compact('case'));
    }

    public function edit(CaseFile $case)
    {
        return view('cases.edit', [
            'case' => $case,
            'templates' => $this->activeTemplates(),
        ]);
    }

    public function update(UpdateCaseRequest $request, CaseFile $case)
    {
        $this->caseService->update($case, $request->validated());

        return redirect()
            ->route('cases.show', ['case' => $case->getRouteKey()])
            ->with('status', 'Kasus berhasil diperbarui.');
    }

    public function destroy(CaseFile $case)
    {
        $this->caseService->delete($case);

        return redirect()
            ->route('cases.index')
            ->with('status', 'Kasus berhasil dihapus.');
    }

    private function activeTemplates()
    {
        return VerificationTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
