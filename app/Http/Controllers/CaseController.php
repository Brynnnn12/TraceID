<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseFile;
use App\Services\CaseService;

class CaseController extends Controller
{
    public function __construct(private readonly CaseService $caseService) {}

    public function index()
    {
        $cases = CaseFile::query()
            ->latest()
            ->paginate(15);

        return view('cases.index', ['cases' => $cases]);
    }

    public function create()
    {
        return view('cases.create');
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
        return view('cases.show', compact('case'));
    }

    public function edit(CaseFile $case)
    {
        return view('cases.edit', compact('case'));
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
}
