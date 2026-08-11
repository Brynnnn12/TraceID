<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseFile;
use App\Models\VerificationTemplate;
use App\Services\CaseService;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function __construct(private readonly CaseService $caseService) {}

    public function index(Request $request)
    {
        $cases = CaseFile::query()
            ->with('template')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('template'), function ($query) use ($request) {
                $query->where('template_id', $request->integer('template'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cases.index', [
            'cases' => $cases,
            'statuses' => CaseStatus::cases(),
            'templates' => $this->activeTemplates(),
            'filters' => [
                'status' => $request->query('status'),
                'template' => $request->query('template'),
            ],
        ]);
    }

    public function create()
    {
        return view('cases.create', [
            'templates' => $this->activeTemplates(),
            'form' => [
                'templateId' => old('template_id', ''),
                'fields' => [], // Kosong untuk create
            ],
        ]);
    }

    public function store(StoreCaseRequest $request, CaseFile $case)
    {
        $case = $this->caseService->create($request->validated());

        return redirect()
            ->route('cases.show', $case)
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

    public function regenerateLink(CaseFile $case)
    {
        $this->caseService->regenerateLink($case);

        return redirect()
            ->route('cases.show', ['case' => $case->getRouteKey()])
            ->with('status', 'Link verifikasi berhasil diregenerasi.');
    }

    public function close(CaseFile $case)
    {
        $this->caseService->close($case);

        return redirect()
            ->route('cases.show', ['case' => $case->getRouteKey()])
            ->with('status', 'Link verifikasi berhasil dinonaktifkan.');
    }

    private function activeTemplates()
    {
        return VerificationTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
